<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\SpmbCandidate;
use App\Models\Student;
use App\Services\SpmbIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpmbCandidateController extends Controller
{
    protected SpmbIntegrationService $service;

    public function __construct(SpmbIntegrationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of SPMB Candidates.
     */
    public function index(Request $request)
    {
        // 1. Get available academic years from SANS Unit master
        $unitAcademicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $unitAcademicYears->firstWhere('is_active', true) ?? $unitAcademicYears->first();

        // Unique yearly academic years for annual entities
        $uniqueAcademicYears = $unitAcademicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Also gather any academic years present in spmb_candidates table
        $spmbDistinctYears = SpmbCandidate::select('academic_year')
            ->whereNotNull('academic_year')
            ->distinct()
            ->pluck('academic_year')
            ->map(function($y) { return str_replace('-', '/', trim($y)); })
            ->filter()
            ->unique();

        // Build unified list of academic year options for SANS Unit
        $academicYearOptions = collect();
        foreach ($uniqueAcademicYears as $ay) {
            $academicYearOptions->push([
                'value' => $ay->name,
                'label' => $ay->name,
                'is_active' => (bool) $ay->has_active,
            ]);
        }
        foreach ($spmbDistinctYears as $sy) {
            if (!$academicYearOptions->contains('value', $sy)) {
                $academicYearOptions->push([
                    'value' => $sy,
                    'label' => $sy,
                    'is_active' => false,
                ]);
            }
        }
        $academicYearOptions = $academicYearOptions->sortByDesc('value')->values();

        // Default period: if request has 'period', use it; otherwise default to active year or 'all'
        $defaultPeriod = $activeAcademicYear ? $activeAcademicYear->name : ($academicYearOptions->first()['value'] ?? 'all');
        $selectedYear = $request->get('period', $defaultPeriod);

        // 2. Base Query
        $query = SpmbCandidate::with('student.classroom');

        if ($selectedYear && $selectedYear !== 'all') {
            $slashYear = str_replace('-', '/', $selectedYear);
            $hyphenYear = str_replace('/', '-', $selectedYear);
            $query->where(function ($q) use ($slashYear, $hyphenYear) {
                $q->where('academic_year', $slashYear)
                  ->orWhere('academic_year', $hyphenYear);
            });
        }

        // Search Filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $statusCol = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'registration_status') ? 'registration_status' : 'spmb_status';
        $paymentCol = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'payment_status') ? 'payment_status' : 'spmb_payment_status';

        // Status Filter
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where($statusCol, $status);
            }
        }

        // Payment Filter
        if ($payment = $request->get('payment_status')) {
            if ($payment !== 'all') {
                $query->where($paymentCol, $payment);
            }
        }

        // Wave Filter
        if ($wave = $request->get('wave')) {
            if ($wave !== 'all') {
                $query->where('wave', $wave);
            }
        }

        // Student Type (Kategori Murid: Reguler / PDBK) Filter
        if ($studentType = $request->get('student_type')) {
            if ($studentType === 'PDBK' || $studentType === 'MBK') {
                $query->where(function ($q) {
                    $q->where('student_type', 'like', '%PDBK%')
                      ->orWhere('student_type', 'like', '%MBK%')
                      ->orWhere('student_type', 'like', '%ABK%')
                      ->orWhere('student_type', 'like', '%INKLUSI%')
                      ->orWhere('target_class', 'like', '%MBK%')
                      ->orWhere('target_class', 'like', '%INKLUSI%')
                      ->orWhereNotNull('special_needs_type');
                });
            } elseif ($studentType === 'REGULER') {
                $query->where(function ($q) {
                    $q->where('student_type', 'like', '%REGULER%')
                      ->orWhereNull('student_type');
                })->where('target_class', 'not like', '%MBK%')
                  ->where('target_class', 'not like', '%INKLUSI%')
                  ->whereNull('special_needs_type');
            }
        }

        // 3. Stats Calculation (based on selected year)
        $statsQuery = SpmbCandidate::query();
        if ($selectedYear && $selectedYear !== 'all') {
            $slashYear = str_replace('-', '/', $selectedYear);
            $hyphenYear = str_replace('/', '-', $selectedYear);
            $statsQuery->where(function ($q) use ($slashYear, $hyphenYear) {
                $q->where('academic_year', $slashYear)
                  ->orWhere('academic_year', $hyphenYear);
            });
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'verified' => (clone $statsQuery)->whereIn($statusCol, ['verified', 'accepted', 'diterima', 'terverifikasi'])->count(),
            'paid' => (clone $statsQuery)->whereIn($paymentCol, ['paid', 'lunas', 'settlement', 'success'])->count(),
            'enrolled' => (clone $statsQuery)->where('is_enrolled', true)->count(),
        ];

        // 4. Get available waves for filter dropdown
        $availableWaves = (clone $statsQuery)->whereNotNull('wave')->distinct()->pluck('wave')->toArray();

        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || (int)$perPage >= 999999) {
            $totalCount = (clone $query)->count();
            $candidates = $query->orderBy('created_at', 'desc')->paginate(max($totalCount, 1))->withQueryString();
        } else {
            $perPageVal = in_array((int)$perPage, [10, 15, 25, 50, 100, 200]) ? (int)$perPage : 15;
            $candidates = $query->orderBy('created_at', 'desc')->paginate($perPageVal)->withQueryString();
        }

        $academicYears = $academicYearOptions->pluck('value')->toArray();

        return view('admin.spmb-candidates.index', compact(
            'candidates', 
            'academicYears', 
            'academicYearOptions', 
            'uniqueAcademicYears',
            'selectedYear', 
            'stats', 
            'availableWaves',
            'activeAcademicYear'
        ));
    }

    /**
     * Show detail of candidate.
     */
    public function show($id): JsonResponse
    {
        $candidate = SpmbCandidate::with('student.classroom.classLevel')->findOrFail($id);
        return response()->json([
            'success' => true,
            'candidate' => $candidate,
            'wa_url' => $candidate->whatsapp_url,
        ]);
    }

    /**
     * Trigger manual pull sync from SPMB.
     */
    public function sync(Request $request): JsonResponse
    {
        $filters = [];
        if ($request->filled('period') && $request->period !== 'all') {
            $filters['period'] = $request->period;
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }

        $result = $this->service->syncCandidates($filters);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Test SPMB connection endpoint.
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->service->testConnection();
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get data prefilled for enrollment modal.
     */
    public function getEnrollData($id): JsonResponse
    {
        $candidate = SpmbCandidate::with('student.classroom')->findOrFail($id);

        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();

        // Match academic year from candidate's period
        $matchedYear = null;
        if ($candidate->academic_year) {
            $cleanYear = str_replace('-', '/', $candidate->academic_year);
            $matchedYear = AcademicYear::where('name', $cleanYear)->first();
        }
        if (!$matchedYear) {
            $matchedYear = AcademicYear::where('is_active', true)->first();
        }

        // Unique yearly academic years for enrollment selector
        $uniqueYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            return [
                'id' => $chosen->id,
                'name' => $chosen->name . ($activeInGroup ? ' (Aktif)' : ''),
                'raw_name' => $chosen->name,
                'is_active' => (bool) $activeInGroup,
            ];
        })->values();

        // Matching academic year IDs (all semesters for that annual year)
        $matchingYearIds = $matchedYear ? $academicYears->where('name', $matchedYear->name)->pluck('id')->toArray() : [];

        // Get active classrooms (including all classrooms with academic_year_id for dynamic client-side filtering)
        $classrooms = Classroom::with(['classLevel', 'homeroomTeacher', 'academicYear'])
            ->withCount(['students as active_students_count' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->where('is_active', true)
            ->orderBy('class_level_id')
            ->orderBy('name')
            ->get();

        // Filter classrooms for the matched academic year if available
        $matchedClassrooms = $classrooms->filter(function($cr) use ($matchingYearIds) {
            return empty($matchingYearIds) || in_array($cr->academic_year_id, $matchingYearIds);
        })->values();

        // If no classrooms matched for future year, fallback to all active classrooms
        $finalClassrooms = $matchedClassrooms->isNotEmpty() ? $matchedClassrooms : $classrooms;

        // Generate suggested NIS for SD (e.g. 26.SD.001 or 27.SD.001)
        $yearDigits = $matchedYear ? substr(explode('/', $matchedYear->name)[0] ?? '2026', -2) : date('y');
        $prefix = "{$yearDigits}.SD.";

        $latestStudent = Student::where('nis', 'like', "{$prefix}%")
            ->orderBy('nis', 'desc')
            ->first();

        $nextSeq = 1;
        if ($latestStudent && preg_match('/(\d+)$/', $latestStudent->nis, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }
        $suggestedNis = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'candidate' => $candidate,
            'student' => $candidate->student,
            'suggested_nis' => $suggestedNis,
            'academic_years' => $uniqueYears,
            'selected_year_id' => $matchedYear?->id,
            'classrooms' => $finalClassrooms,
            'all_classrooms' => $classrooms,
        ]);
    }

    /**
     * Enroll candidate into active students.
     */
    public function enroll(Request $request, $id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis,' . ($candidate->student_id ?? 'NULL'),
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrolled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $studentData = [
            'nis' => $validated['nis'],
            'nisn' => $candidate->nisn,
            'nik' => $candidate->nik,
            'no_kk' => $candidate->no_kk,
            'spmb_candidate_id' => $candidate->id,
            'classroom_id' => $validated['classroom_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'full_name' => $candidate->full_name,
            'nickname' => $candidate->nickname,
            'gender' => in_array(strtolower((string)$candidate->gender), ['female', 'p', 'perempuan']) ? 'P' : 'L',
            'student_type' => $candidate->student_type ?? 'REGULER',
            'special_needs_type' => $candidate->special_needs_type,
            'birth_place' => $candidate->birth_place,
            'birth_date' => $candidate->birth_date,
            'religion' => $candidate->religion ?: 'Islam',
            'address' => $candidate->address,
            'rt' => $candidate->rt,
            'rw' => $candidate->rw,
            'village' => $candidate->village,
            'district' => $candidate->district,
            'city' => $candidate->city,
            'province' => $candidate->province,
            'postal_code' => $candidate->postal_code,
            'child_number' => $candidate->child_number,
            'siblings_count' => $candidate->siblings_count,
            'blood_type' => $candidate->blood_type,
            'weight' => $candidate->weight,
            'height' => $candidate->height,
            'previous_school' => $candidate->previous_school,
            'previous_school_address' => $candidate->previous_school_address,
            'student_photo_url' => $candidate->student_photo_url,
            'father_name' => $candidate->father_name,
            'father_nik' => $candidate->father_nik,
            'father_phone' => $candidate->father_phone,
            'father_job' => $candidate->father_job,
            'father_education' => $candidate->father_education,
            'mother_name' => $candidate->mother_name,
            'mother_nik' => $candidate->mother_nik,
            'mother_phone' => $candidate->mother_phone,
            'mother_job' => $candidate->mother_job,
            'mother_education' => $candidate->mother_education,
            'guardian_name' => $candidate->guardian_name,
            'guardian_phone' => $candidate->guardian_phone,
            'parent_phone' => $candidate->parent_phone,
            'parent_email' => $candidate->parent_email,
            'documents' => $candidate->documents,
            'status' => 'aktif',
            'enrolled_date' => $validated['enrolled_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? "Terdaftar via integrasi SPMB ({$candidate->registration_number})",
        ];

        if ($candidate->student_id && $existingStudent = Student::find($candidate->student_id)) {
            $existingStudent->update($studentData);
            $student = $existingStudent;
        } else {
            $student = Student::create($studentData);
        }

        $candidate->is_enrolled = true;
        $candidate->enrolled_at = now();
        $candidate->student_id = $student->id;
        $candidate->save();

        // Rekam riwayat rombel / enrollment history
        $student->load(['classroom.classLevel', 'classroom.homeroomTeacher']);
        \App\Models\StudentClassroomHistory::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $validated['academic_year_id'],
            ],
            [
                'classroom_id' => $validated['classroom_id'],
                'classroom_name' => $student->classroom ? $student->classroom->name : null,
                'grade_level' => $student->classroom && $student->classroom->classLevel ? $student->classroom->classLevel->name : '1',
                'homeroom_teacher_name' => $student->classroom && $student->classroom->homeroomTeacher ? $student->classroom->homeroomTeacher->name : null,
                'status' => 'aktif',
                'start_date' => $validated['enrolled_date'] ?? now()->toDateString(),
                'notes' => 'Penerimaan Siswa Baru SPMB',
            ]
        );

        $appName = function_exists('setting') ? setting('app_name', 'SD Anak Saleh') : 'SD Anak Saleh';

        return response()->json([
            'success' => true,
            'message' => "Ananda {$candidate->full_name} berhasil resmi terdaftar sebagai Siswa Aktif {$appName} (NIS: {$student->nis}).",
            'student' => $student,
        ]);
    }

    /**
     * Cancel enrollment status and remove student record.
     */
    public function unenroll($id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);

        if ($candidate->student_id) {
            $student = Student::find($candidate->student_id);
            if ($student) {
                $student->delete();
            }
        }

        $candidate->is_enrolled = false;
        $candidate->enrolled_at = null;
        $candidate->student_id = null;
        $candidate->save();

        return response()->json([
            'success' => true,
            'message' => "Status Siswa Aktif untuk {$candidate->full_name} berhasil dibatalkan.",
        ]);
    }

    /**
     * Update SPMB Candidate data.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|max:30',
            'student_type' => 'nullable|string|max:50',
            'special_needs_type' => 'nullable|string|max:255',
            'target_class' => 'nullable|string|max:100',
            'academic_year' => 'nullable|string|max:50',
            'wave' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
            'father_job' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'mother_job' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:50',
            'parent_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'previous_school' => 'nullable|string|max:255',
            'spmb_status' => 'nullable|string|max:50',
            'registration_status' => 'nullable|string|max:50',
            'spmb_payment_status' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|max:50',
        ]);

        // Normalize student_type
        if ($request->has('student_type')) {
            $stUpper = strtoupper(trim((string)$request->input('student_type')));
            $validated['student_type'] = (str_contains($stUpper, 'PDBK') || str_contains($stUpper, 'MBK') || str_contains($stUpper, 'ABK') || str_contains($stUpper, 'INKLUSI')) ? 'PDBK' : 'REGULER';
        }

        // Normalize gender
        if (!empty($validated['gender'])) {
            $g = strtolower($validated['gender']);
            if (in_array($g, ['l', 'laki-laki', 'male'])) {
                $validated['gender'] = 'male';
            } elseif (in_array($g, ['p', 'perempuan', 'female'])) {
                $validated['gender'] = 'female';
            }
        }

        // Handle status column compatibility
        $hasRegStatus = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'registration_status');
        $hasSpmbStatus = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'spmb_status');
        $statusVal = $request->input('registration_status', $request->input('spmb_status'));
        if ($statusVal) {
            if ($hasRegStatus) $validated['registration_status'] = $statusVal;
            if ($hasSpmbStatus) $validated['spmb_status'] = $statusVal;
        }

        $hasPayStatus = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'payment_status');
        $hasSpmbPayStatus = \Illuminate\Support\Facades\Schema::hasColumn('spmb_candidates', 'spmb_payment_status');
        $payVal = $request->input('payment_status', $request->input('spmb_payment_status'));
        if ($payVal) {
            if ($hasPayStatus) $validated['payment_status'] = $payVal;
            if ($hasSpmbPayStatus) $validated['spmb_payment_status'] = $payVal;
        }

        $candidate->update($validated);

        // If enrolled to an active student, sync matching fields
        if ($candidate->student_id && ($student = Student::find($candidate->student_id))) {
            $studentUpdate = [
                'full_name' => $candidate->full_name,
                'nickname' => $candidate->nickname,
                'gender' => $candidate->gender === 'female' ? 'P' : 'L',
                'birth_place' => $candidate->birth_place,
                'birth_date' => $candidate->birth_date,
                'nik' => $candidate->nik,
                'nisn' => $candidate->nisn,
                'father_name' => $candidate->father_name,
                'father_phone' => $candidate->father_phone,
                'father_job' => $candidate->father_job,
                'mother_name' => $candidate->mother_name,
                'mother_phone' => $candidate->mother_phone,
                'mother_job' => $candidate->mother_job,
                'guardian_name' => $candidate->guardian_name,
                'guardian_phone' => $candidate->guardian_phone,
                'parent_phone' => $candidate->parent_phone,
                'address' => $candidate->address,
                'city' => $candidate->city,
                'province' => $candidate->province,
                'previous_school' => $candidate->previous_school,
            ];
            $student->update(array_filter($studentUpdate, fn($v) => !is_null($v)));
        }

        return response()->json([
            'success' => true,
            'message' => "Data pendaftar {$candidate->full_name} berhasil diperbarui.",
            'candidate' => $candidate->fresh(['student.classroom']),
        ]);
    }

    /**
     * Delete SPMB Candidate.
     */
    public function destroy($id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);
        $name = $candidate->full_name;

        // 1. Unlink any student record referencing this candidate
        Student::where('spmb_candidate_id', $candidate->id)->update(['spmb_candidate_id' => null]);

        // 2. If candidate is linked to a student, delete student & student histories cleanly
        if ($candidate->student_id) {
            $studentId = $candidate->student_id;
            $candidate->student_id = null;
            $candidate->save();

            $student = Student::find($studentId);
            if ($student) {
                \App\Models\StudentClassroomHistory::where('student_id', $student->id)->delete();
                $student->delete();
            }
        }

        $candidate->delete();

        return response()->json([
            'success' => true,
            'message' => "Data calon pendaftar {$name} berhasil dihapus.",
        ]);
    }
}
