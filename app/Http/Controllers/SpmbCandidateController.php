<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\SpmbCandidate;
use App\Models\Student;
use App\Services\SpmbIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // 1. Get dynamic master filter options from SPMB API / master data
        $masterOptions = $this->service->getFilterOptions();
        $defaultPeriodFromSpmb = $masterOptions['default_period'] ?? null;

        $rawCandidateYears = SpmbCandidate::whereNotNull('academic_year')
            ->distinct()
            ->pluck('academic_year')
            ->toArray();
        $rawMasterYears = AcademicYear::pluck('name')->toArray();
        $apiPeriods = $masterOptions['periods'] ?? [];

        $academicYears = collect(array_merge($rawCandidateYears, $rawMasterYears, $apiPeriods))
            ->map(fn($y) => str_replace('-', '/', trim($y)))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        // Default year selection priority:
        // 1. User query parameter 'period'
        // 2. Default active registration period in SPMB (e.g. 2027/2028)
        // 3. Most populated candidate year in database
        // 4. Active academic year in SD
        $defaultYear = $defaultPeriodFromSpmb;
        if (!$defaultYear || !in_array($defaultYear, $academicYears)) {
            $mostPopulatedYear = SpmbCandidate::whereNotNull('academic_year')
                ->select('academic_year', DB::raw('count(*) as total'))
                ->groupBy('academic_year')
                ->orderByDesc('total')
                ->value('academic_year');
            
            $activeMasterYear = AcademicYear::where('is_active', true)->value('name');

            $defaultYear = $mostPopulatedYear ?: ($activeMasterYear ?: ($academicYears[0] ?? 'all'));
        }

        $selectedYear = $request->get('period', $defaultYear);
        if ($selectedYear && $selectedYear !== 'all') {
            $selectedYear = str_replace('-', '/', trim($selectedYear));
        }

        // 2. Base Query
        $query = SpmbCandidate::with('student.classroom.classLevel');

        if ($selectedYear && $selectedYear !== 'all') {
            $slashYear = str_replace('-', '/', $selectedYear);
            $hyphenYear = str_replace('/', '-', $selectedYear);
            $query->whereIn('academic_year', [$slashYear, $hyphenYear]);
        }

        // Search Filter (Keyword search for name, reg no, NIK, NISN, parent, phone)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('previous_school', 'like', "%{$search}%");
            });
        }

        // 1. Filter Jalur Masuk (registration_type)
        if ($regType = $request->get('registration_type')) {
            if ($regType !== 'all') {
                $query->where(function($q) use ($regType) {
                    $q->where('raw_payload', 'like', "%\"registration_type\":\"{$regType}\"%")
                      ->orWhere('raw_payload', 'like', "%\"entry_type\":\"{$regType}\"%");
                });
            }
        }

        // 2. Filter Gelombang (wave)
        if ($wave = $request->get('wave')) {
            if ($wave !== 'all') {
                $query->where('wave', $wave);
            }
        }

        // 3. Filter Target Kelas (target_class / admission_level)
        if ($admissionLevel = $request->get('admission_level')) {
            if ($admissionLevel !== 'all') {
                $query->where(function ($q) use ($admissionLevel) {
                    $q->where('target_class', $admissionLevel)
                      ->orWhere('target_class', 'like', "%{$admissionLevel}%")
                      ->orWhere('raw_payload', 'like', "%\"target_class\":\"{$admissionLevel}\"%")
                      ->orWhere('raw_payload', 'like', "%\"admission_level\":\"{$admissionLevel}\"%");
                });
            }
        }

        // 4. Filter Kategori Siswa (student_type: REGULER / PDBK MBK Inklusi)
        if ($category = $request->get('student_type', $request->get('category'))) {
            if ($category !== 'all' && !empty($category)) {
                if (in_array(strtoupper($category), ['PDBK', 'MBK', 'ABK', 'INKLUSI'])) {
                    $query->where(function ($q) {
                        $q->where('student_type', 'like', '%PDBK%')
                          ->orWhere('student_type', 'like', '%MBK%')
                          ->orWhere('student_type', 'like', '%ABK%')
                          ->orWhere('target_class', 'like', '%MBK%')
                          ->orWhere('target_class', 'like', '%INKLUSI%')
                          ->orWhereNotNull('special_needs_type');
                    });
                } else {
                    $query->where(function ($q) {
                        $q->where('student_type', 'like', '%REGULER%')
                          ->orWhereNull('student_type');
                    })->where('target_class', 'not like', '%MBK%')
                      ->where('target_class', 'not like', '%INKLUSI%')
                      ->whereNull('special_needs_type');
                }
            }
        }

        // 5. Filter Status Pendaftaran (verified, accepted, agreement_signed, completed, pending)
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('spmb_status', $status);
            }
        }

        // 6. Filter Status Pembayaran (paid, unpaid, pending)
        if ($payment = $request->get('payment_status')) {
            if ($payment !== 'all') {
                $query->where('spmb_payment_status', $payment);
            }
        }

        // 3. Stats Calculation (based on selected year)
        $statsQuery = SpmbCandidate::query();
        if ($selectedYear && $selectedYear !== 'all') {
            $slashYear = str_replace('-', '/', $selectedYear);
            $hyphenYear = str_replace('/', '-', $selectedYear);
            $statsQuery->whereIn('academic_year', [$slashYear, $hyphenYear]);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'verified' => (clone $statsQuery)->whereIn('spmb_status', ['verified', 'accepted', 'diterima', 'terverifikasi', 'completed', 'agreement_signed'])->count(),
            'paid' => (clone $statsQuery)->whereIn('spmb_payment_status', ['paid', 'lunas', 'settlement', 'success'])->count(),
            'enrolled' => (clone $statsQuery)->where('is_enrolled', true)->count(),
            'has_payment_data' => (clone $statsQuery)->where(function($q) {
                $q->whereNotNull('spmb_payment_status')
                  ->orWhereNotNull('payments_data');
            })->exists(),
        ];

        // 4. Dynamic Filter Options Lists
        $dbTypes = SpmbCandidate::whereNotNull('raw_payload')->pluck('raw_payload')->map(function($p) {
            return is_array($p) ? ($p['registration_type'] ?? ($p['entry_type'] ?? null)) : null;
        })->filter()->unique()->toArray();
        $apiTypes = $masterOptions['registration_types'] ?? ['Murid Baru', 'Mutasi Masuk / Pindahan'];
        $registrationTypes = array_values(array_unique(array_filter(array_merge($apiTypes, $dbTypes))));

        $dbWaves = SpmbCandidate::whereNotNull('wave')->where('wave', '!=', '')->distinct()->pluck('wave')->toArray();
        $apiWaves = $masterOptions['waves'] ?? ['Indent', 'Gelombang 1', 'Gelombang 2'];
        $availableWaves = array_values(array_unique(array_filter(array_merge($apiWaves, $dbWaves))));

        $allMasterGrades = $masterOptions['grades'] ?? [
            ['name' => 'Kelas 1', 'jenjang_code' => 'SD'],
            ['name' => 'Kelas 2', 'jenjang_code' => 'SD'],
            ['name' => 'Kelas 3', 'jenjang_code' => 'SD'],
            ['name' => 'Kelas 4', 'jenjang_code' => 'SD'],
            ['name' => 'Kelas 5', 'jenjang_code' => 'SD'],
            ['name' => 'Kelas 6', 'jenjang_code' => 'SD'],
        ];
        $availableAdmissionLevels = array_values(array_unique(array_column($allMasterGrades, 'name')));

        $categories = ['Reguler', 'PDBK (Inklusi)'];

        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || (int)$perPage >= 999999) {
            $totalCount = (clone $query)->count();
            $candidates = $query->orderBy('created_at', 'desc')->paginate(max($totalCount, 1))->withQueryString();
        } else {
            $perPageVal = in_array((int)$perPage, [10, 15, 25, 50, 100, 200]) ? (int)$perPage : 15;
            $candidates = $query->orderBy('created_at', 'desc')->paginate($perPageVal)->withQueryString();
        }

        $masterAcademicYears = AcademicYear::orderBy('name', 'desc')->get();
        $masterClassrooms = Classroom::with(['classLevel', 'homeroomTeacher'])
            ->where('is_active', true)
            ->orderBy('class_level_id')
            ->orderBy('name')
            ->get();

        return view('admin.spmb-candidates.index', compact(
            'candidates', 
            'academicYears', 
            'selectedYear', 
            'stats', 
            'registrationTypes',
            'availableWaves',
            'allMasterGrades',
            'availableAdmissionLevels',
            'categories',
            'masterAcademicYears',
            'masterClassrooms'
        ));
    }

    /**
     * Show detail of candidate.
     */
    public function show($id): JsonResponse
    {
        $candidate = SpmbCandidate::with([
            'student.classroom.classLevel',
            'student.classroom.homeroomTeacher',
            'student.academicYear'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'candidate' => $candidate,
            'clean_phone' => $candidate->getCleanPhone(),
            'wa_url' => $candidate->whatsapp_url,
        ]);
    }

    /**
     * Trigger manual pull sync from SPMB.
     */
    public function sync(Request $request): JsonResponse
    {
        $period = $request->input('period');
        $status = $request->input('status');

        $result = $this->service->syncCandidates($period, $status);

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
        $candidate = SpmbCandidate::with('student.classroom.classLevel')->findOrFail($id);

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Match academic year from candidate's period
        $matchedYear = null;
        if ($candidate->academic_year) {
            $cleanYear = str_replace('-', '/', $candidate->academic_year);
            $matchedYear = AcademicYear::where('name', $cleanYear)->first();
        }
        if (!$matchedYear) {
            $matchedYear = AcademicYear::where('is_active', true)->first() ?? $academicYears->first();
        }

        // Get active classrooms with count of active students
        $classrooms = Classroom::with(['classLevel', 'homeroomTeacher'])
            ->withCount(['students as active_students_count' => function ($q) use ($matchedYear) {
                $q->where('status', 'aktif');
                if ($matchedYear) {
                    $q->where('academic_year_id', $matchedYear->id);
                }
            }])
            ->where('is_active', true)
            ->orderBy('class_level_id')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        $classLevels = \App\Models\ClassLevel::orderBy('order')->get();

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
            'academic_years' => $academicYears,
            'selected_year_id' => $matchedYear?->id,
            'class_levels' => $classLevels,
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Enroll candidate into active students.
     */
    public function enroll(Request $request, $id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'nullable|string|max:50|unique:students,nis,' . ($candidate->student_id ?? 'NULL'),
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrolled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $classroom = Classroom::with('classLevel')->findOrFail($validated['classroom_id']);
        $classLevelId = $classroom->class_level_id;

        $studentData = [
            'nis' => !empty($validated['nis']) ? trim($validated['nis']) : null,
            'nisn' => $candidate->nisn,
            'nik' => $candidate->nik,
            'no_kk' => $candidate->no_kk,
            'spmb_candidate_id' => $candidate->id,
            'class_level_id' => $classLevelId,
            'classroom_id' => $classroom->id,
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
        if ($student->classroom_id) {
            $student->load(['classroom.classLevel', 'classroom.homeroomTeacher']);
            \App\Models\StudentClassroomHistory::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $student->academic_year_id,
                    'classroom_id' => $student->classroom_id,
                ],
                [
                    'classroom_name' => $student->classroom ? $student->classroom->name : null,
                    'grade_level' => $student->classroom && $student->classroom->classLevel ? $student->classroom->classLevel->name : '1',
                    'homeroom_teacher_name' => $student->classroom && $student->classroom->homeroomTeacher ? $student->classroom->homeroomTeacher->name : null,
                    'status' => 'aktif',
                    'start_date' => $student->enrolled_date ?? now()->toDateString(),
                    'notes' => "Pendaftaran Siswa Baru via SPMB ({$candidate->registration_number})",
                ]
            );
        }

        $unitName = function_exists('setting') ? setting('unit_name', 'SD Anak Saleh Malang') : 'SD Anak Saleh Malang';

        return response()->json([
            'success' => true,
            'message' => "Ananda {$candidate->full_name} berhasil resmi terdaftar sebagai Siswa Aktif {$unitName} (NIS: {$student->nis}).",
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
                \App\Models\StudentClassroomHistory::where('student_id', $student->id)->delete();
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
            'no_kk' => 'nullable|string|max:30',
            'student_type' => 'nullable|string|max:50',
            'special_needs_type' => 'nullable|string|max:255',
            'target_class' => 'nullable|string|max:100',
            'academic_year' => 'nullable|string|max:50',
            'wave' => 'nullable|string|max:100',
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|max:30',
            'father_phone' => 'nullable|string|max:50',
            'father_job' => 'nullable|string|max:100',
            'father_education' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|max:30',
            'mother_phone' => 'nullable|string|max:50',
            'mother_job' => 'nullable|string|max:100',
            'mother_education' => 'nullable|string|max:100',
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

        if (!empty($validated['academic_year'])) {
            $validated['academic_year'] = str_replace('-', '/', trim($validated['academic_year']));
        }

        $candidate->update($validated);

        // If enrolled to an active student, sync matching fields
        if ($candidate->student_id && ($student = Student::find($candidate->student_id))) {
            $studentUpdate = [
                'full_name' => $candidate->full_name,
                'nickname' => $candidate->nickname,
                'gender' => in_array($candidate->gender, ['female', 'P']) ? 'P' : 'L',
                'birth_place' => $candidate->birth_place,
                'birth_date' => $candidate->birth_date,
                'nik' => $candidate->nik,
                'nisn' => $candidate->nisn,
                'no_kk' => $candidate->no_kk,
                'student_type' => $candidate->student_type,
                'special_needs_type' => $candidate->special_needs_type,
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
