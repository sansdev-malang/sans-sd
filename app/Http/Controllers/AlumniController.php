<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Display Alumni Ledger (Graduated Students).
     */
    public function index(Request $request)
    {
        // Only students with status 'lulus'
        $query = Student::with([
            'classroom.classLevel',
            'academicYear',
            'classroomHistories.classroom',
            'classroomHistories.academicYear'
        ])->where('status', 'lulus');

        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $uniqueAcademicYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Filter: Academic Year (Tahun Ajaran Kelulusan)
        if ($academicYearId = $request->get('academic_year_id')) {
            if ($academicYearId !== 'all') {
                $selectedYear = $academicYears->firstWhere('id', $academicYearId);
                $matchingIds = $selectedYear ? $academicYears->where('name', $selectedYear->name)->pluck('id') : [$academicYearId];
                $query->whereIn('academic_year_id', $matchingIds);
            }
        }

        // Filter: Graduation Year (Year string fallback)
        if ($gradYear = $request->get('graduation_year')) {
            if ($gradYear !== 'all') {
                $query->where('graduation_year', $gradYear);
            }
        }

        // Filter: Gender
        if ($gender = $request->get('gender')) {
            if ($gender !== 'all') {
                $query->where('gender', $gender);
            }
        }

        // Filter: Search NIS, NISN, NIK, Name, Diploma Number, Secondary School
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('diploma_number', 'like', "%{$search}%")
                  ->orWhere('continued_school', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%");
            });
        }

        // Stats
        $alumniQuery = Student::where('status', 'lulus');
        $stats = [
            'total_alumni' => (clone $alumniQuery)->count(),
            'male' => (clone $alumniQuery)->whereIn('gender', ['L', 'Laki-laki', 'Male'])->count(),
            'female' => (clone $alumniQuery)->whereIn('gender', ['P', 'Perempuan', 'Female'])->count(),
            'with_diploma' => (clone $alumniQuery)->whereNotNull('diploma_number')->where('diploma_number', '!=', '')->count(),
            'with_continued_school' => (clone $alumniQuery)->whereNotNull('continued_school')->where('continued_school', '!=', '')->count(),
        ];

        $students = $query->orderBy('academic_year_id', 'desc')
            ->orderBy('full_name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.alumni.index', [
            'students' => $students,
            'stats' => $stats,
            'academicYears' => $uniqueAcademicYears,
        ]);
    }

    /**
     * Show single Alumni detail (JSON).
     */
    public function show($id): JsonResponse
    {
        $student = Student::with([
            'classroom.classLevel',
            'academicYear',
            'spmbCandidate',
            'classroomHistories.classroom.classLevel',
            'classroomHistories.classroom.homeroomTeacher',
            'classroomHistories.academicYear'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'student' => $student,
            'formatted_gender' => $student->formatted_gender,
            'age' => $student->age,
            'classroom_histories' => $student->classroomHistories,
        ]);
    }

    /**
     * Print official Student Main Book / Alumni Record (A4 Printable View).
     */
    public function print($id)
    {
        $student = Student::with([
            'classroom.classLevel',
            'academicYear',
            'classroomHistories.classroom.classLevel',
            'classroomHistories.classroom.homeroomTeacher',
            'classroomHistories.academicYear'
        ])->findOrFail($id);

        return view('admin.alumni.print', compact('student'));
    }

    /**
     * Update Alumni record attributes.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'graduation_year' => 'nullable|string|max:20',
            'diploma_number' => 'nullable|string|max:100',
            'continued_school' => 'nullable|string|max:255',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Data Alumni {$student->full_name} berhasil diperbarui.",
            'student' => $student,
        ]);
    }
}
