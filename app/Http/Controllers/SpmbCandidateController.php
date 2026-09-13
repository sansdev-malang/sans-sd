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
        // 1. Get available academic years
        $academicYears = SpmbCandidate::select('academic_year')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year')
            ->toArray();

        // Default to latest year or 'all' if empty
        $selectedYear = $request->get('period', $academicYears[0] ?? 'all');

        // 2. Base Query
        $query = SpmbCandidate::with('student.classroom');

        if ($selectedYear && $selectedYear !== 'all') {
            $query->where('academic_year', $selectedYear);
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

        // 3. Stats Calculation (based on selected year)
        $statsQuery = SpmbCandidate::query();
        if ($selectedYear && $selectedYear !== 'all') {
            $statsQuery->where('academic_year', $selectedYear);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'verified' => (clone $statsQuery)->whereIn($statusCol, ['verified', 'accepted', 'diterima', 'terverifikasi'])->count(),
            'paid' => (clone $statsQuery)->whereIn($paymentCol, ['paid', 'lunas', 'settlement', 'success'])->count(),
            'enrolled' => (clone $statsQuery)->where('is_enrolled', true)->count(),
        ];

        // 4. Get available waves for filter dropdown
        $availableWaves = (clone $statsQuery)->whereNotNull('wave')->distinct()->pluck('wave')->toArray();

        $candidates = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.spmb-candidates.index', compact('candidates', 'academicYears', 'selectedYear', 'stats', 'availableWaves'));
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

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Match academic year from candidate's period
        $matchedYear = null;
        if ($candidate->academic_year) {
            $cleanYear = str_replace('-', '/', $candidate->academic_year);
            $matchedYear = AcademicYear::where('name', $cleanYear)->first();
        }
        if (!$matchedYear) {
            $matchedYear = AcademicYear::where('is_active', true)->first();
        }

        // Get active classrooms
        $classrooms = Classroom::with(['classLevel', 'homeroomTeacher'])
            ->withCount(['students as active_students_count' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->where('is_active', true)
            ->when($matchedYear, function ($q) use ($matchedYear) {
                $q->where('academic_year_id', $matchedYear->id);
            })
            ->orderBy('class_level_id')
            ->orderBy('name')
            ->get();

        // Generate suggested NIS for SD (e.g. 27.SD.001)
        $yearDigits = $matchedYear ? substr(explode('/', $matchedYear->name)[0] ?? '2027', -2) : date('y');
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
            'spmb_candidate_id' => $candidate->id,
            'classroom_id' => $validated['classroom_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'full_name' => $candidate->full_name,
            'nickname' => $candidate->nickname,
            'gender' => $candidate->gender,
            'birth_place' => $candidate->birth_place,
            'birth_date' => $candidate->birth_date,
            'religion' => $candidate->religion ?: 'Islam',
            'address' => $candidate->address,
            'city' => $candidate->city,
            'province' => $candidate->province,
            'previous_school' => $candidate->previous_school,
            'student_photo_url' => $candidate->student_photo_url,
            'father_name' => $candidate->father_name,
            'father_phone' => $candidate->father_phone,
            'father_job' => $candidate->father_job,
            'mother_name' => $candidate->mother_name,
            'mother_phone' => $candidate->mother_phone,
            'mother_job' => $candidate->mother_job,
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
}
