<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroomHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassPromotionController extends Controller
{
    /**
     * Display Class Promotion & Graduation management page.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        $sourceYearId = $request->get('source_year_id', $activeAcademicYear?->id);
        $sourceYear = $academicYears->firstWhere('id', $sourceYearId) ?? $activeAcademicYear;

        // Classrooms in source academic year
        $sourceClassrooms = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])
            ->where('is_active', true)
            ->where('academic_year_id', $sourceYear?->id)
            ->orderBy('class_level_id')
            ->orderBy('name')
            ->get();

        // All active classrooms across all academic years for target selection
        $allClassrooms = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])
            ->where('is_active', true)
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('class_level_id')
            ->orderBy('name')
            ->get();

        // Grade 6 classrooms for graduation tab
        $grade6Classrooms = $sourceClassrooms->filter(function ($c) {
            return ($c->classLevel && ($c->classLevel->order == 6 || $c->classLevel->code == '6' || str_contains($c->classLevel->name, '6'))) 
                || str_starts_with($c->code, '6') 
                || str_starts_with($c->name, '6');
        });

        $classLevels = ClassLevel::orderBy('order')->get();

        return view('admin.promotions.index', compact(
            'academicYears',
            'activeAcademicYear',
            'sourceYear',
            'sourceClassrooms',
            'allClassrooms',
            'grade6Classrooms',
            'classLevels'
        ));
    }

    /**
     * Fetch active students in a classroom for promotion table (JSON).
     */
    public function getStudents(Request $request): JsonResponse
    {
        $classroomId = $request->get('classroom_id');
        if (!$classroomId) {
            return response()->json(['success' => false, 'message' => 'Rombel belum dipilih.', 'students' => []]);
        }

        $classroom = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])->find($classroomId);
        if (!$classroom) {
            return response()->json(['success' => false, 'message' => 'Rombel tidak ditemukan.', 'students' => []]);
        }

        $students = Student::where('classroom_id', $classroomId)
            ->where('status', 'aktif')
            ->orderBy('full_name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'classroom' => $classroom,
            'students' => $students,
            'total' => $students->count(),
        ]);
    }

    /**
     * Process Batch Class Promotion / Roll-over.
     */
    public function promote(Request $request): JsonResponse
    {
        $request->validate([
            'source_classroom_id' => 'required|exists:classrooms,id',
            'target_academic_year_id' => 'required|exists:academic_years,id',
            'default_target_classroom_id' => 'nullable|exists:classrooms,id',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.action' => 'required|in:promote,stay,transfer_out',
            'students.*.target_classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $sourceClassroom = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])->findOrFail($request->source_classroom_id);
        $targetAcademicYear = AcademicYear::findOrFail($request->target_academic_year_id);
        $defaultTargetClassroom = $request->default_target_classroom_id ? Classroom::with(['classLevel', 'homeroomTeacher'])->find($request->default_target_classroom_id) : null;

        $promotedCount = 0;
        $stayedCount = 0;
        $transferredCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->students as $item) {
                $student = Student::findOrFail($item['student_id']);
                $action = $item['action'];
                $customTargetId = !empty($item['target_classroom_id']) ? $item['target_classroom_id'] : null;

                if ($action === 'promote') {
                    $targetClassroomId = $customTargetId ?: $defaultTargetClassroom?->id;
                    if (!$targetClassroomId) {
                        throw new \Exception("Rombel tujuan belum dipilih untuk siswa {$student->full_name}.");
                    }

                    $destClassroom = Classroom::with(['classLevel', 'homeroomTeacher'])->findOrFail($targetClassroomId);

                    // 1. Close out existing history in source academic year
                    StudentClassroomHistory::where('student_id', $student->id)
                        ->where('academic_year_id', $sourceClassroom->academic_year_id)
                        ->update([
                            'status' => 'naik_kelas',
                            'end_date' => now()->toDateString(),
                        ]);

                    // 2. Update Student model
                    $student->update([
                        'classroom_id' => $destClassroom->id,
                        'academic_year_id' => $targetAcademicYear->id,
                        'status' => 'aktif',
                    ]);

                    // 3. Create or update new history record in target academic year with snapshot
                    StudentClassroomHistory::updateOrCreate([
                        'student_id' => $student->id,
                        'academic_year_id' => $targetAcademicYear->id,
                    ], [
                        'classroom_id' => $destClassroom->id,
                        'grade_level' => $destClassroom->classLevel?->name ?? ($destClassroom->classLevel?->order ? 'Kelas ' . $destClassroom->classLevel->order : substr($destClassroom->name, 0, 1)),
                        'classroom_name' => $destClassroom->name,
                        'homeroom_teacher_name' => $destClassroom->homeroomTeacher?->name,
                        'status' => 'aktif',
                        'start_date' => now()->toDateString(),
                        'notes' => "Naik kelas dari {$sourceClassroom->name}",
                    ]);

                    $promotedCount++;
                } elseif ($action === 'stay') {
                    // Tinggal kelas / repeat
                    $stayTargetClassroomId = $customTargetId ?: $sourceClassroom->id;
                    $destClassroom = Classroom::with(['classLevel', 'homeroomTeacher'])->find($stayTargetClassroomId) ?: $sourceClassroom;

                    // 1. Close out existing history
                    StudentClassroomHistory::where('student_id', $student->id)
                        ->where('academic_year_id', $sourceClassroom->academic_year_id)
                        ->update([
                            'status' => 'tinggal_kelas',
                            'end_date' => now()->toDateString(),
                        ]);

                    // 2. Update Student model
                    $student->update([
                        'classroom_id' => $destClassroom->id,
                        'academic_year_id' => $targetAcademicYear->id,
                        'status' => 'aktif',
                    ]);

                    // 3. Create new history record
                    StudentClassroomHistory::updateOrCreate([
                        'student_id' => $student->id,
                        'academic_year_id' => $targetAcademicYear->id,
                    ], [
                        'classroom_id' => $destClassroom->id,
                        'grade_level' => $destClassroom->classLevel?->name ?? ($destClassroom->classLevel?->order ? 'Kelas ' . $destClassroom->classLevel->order : substr($destClassroom->name, 0, 1)),
                        'classroom_name' => $destClassroom->name,
                        'homeroom_teacher_name' => $destClassroom->homeroomTeacher?->name,
                        'status' => 'aktif',
                        'start_date' => now()->toDateString(),
                        'notes' => "Tinggal kelas / mengulang di {$destClassroom->name}",
                    ]);

                    $stayedCount++;
                } elseif ($action === 'transfer_out') {
                    // Mutasi keluar
                    StudentClassroomHistory::where('student_id', $student->id)
                        ->where('academic_year_id', $sourceClassroom->academic_year_id)
                        ->update([
                            'status' => 'mutasi_keluar',
                            'end_date' => now()->toDateString(),
                        ]);

                    $student->update([
                        'status' => 'mutasi',
                        'notes' => 'Mutasi keluar pada pergantian tahun ajaran ' . $targetAcademicYear->name,
                    ]);

                    $transferredCount++;
                }
            }

            DB::commit();

            $message = "Proses berhasil! {$promotedCount} siswa naik kelas";
            if ($stayedCount > 0) $message .= ", {$stayedCount} siswa tinggal kelas";
            if ($transferredCount > 0) $message .= ", {$transferredCount} siswa mutasi keluar";
            $message .= " ke Tahun Ajaran {$targetAcademicYear->name}.";

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process Grade 6 Graduation.
     */
    public function graduate(Request $request): JsonResponse
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'graduation_year' => 'nullable|string|max:20',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.diploma_number' => 'nullable|string|max:100',
        ]);

        $classroom = Classroom::with(['academicYear'])->findOrFail($request->classroom_id);
        $academicYearId = $request->academic_year_id ?: $classroom->academic_year_id;
        $academicYear = AcademicYear::find($academicYearId) ?: $classroom->academicYear;
        $gradYearString = $request->graduation_year ?: ($academicYear ? $academicYear->name : date('Y'));
        $graduatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->students as $item) {
                $student = Student::findOrFail($item['student_id']);
                $diplomaNumber = !empty($item['diploma_number']) ? trim($item['diploma_number']) : null;

                // 1. Update Student status to 'lulus' and link to graduation academic year
                $student->update([
                    'status' => 'lulus',
                    'academic_year_id' => $academicYearId,
                    'graduation_year' => $gradYearString,
                    'diploma_number' => $diplomaNumber,
                ]);

                // 2. Update StudentClassroomHistory
                StudentClassroomHistory::where('student_id', $student->id)
                    ->where('classroom_id', $classroom->id)
                    ->update([
                        'status' => 'lulus',
                        'end_date' => now()->toDateString(),
                        'notes' => "Lulus Tahun Pelajaran {$gradYearString}" . ($diplomaNumber ? " (No. Ijazah: {$diplomaNumber})" : ""),
                    ]);

                $graduatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Proses kelulusan selesai! {$graduatedCount} siswa rombel {$classroom->name} berhasil diluluskan (Tahun Pelajaran {$gradYearString}).",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses kelulusan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
