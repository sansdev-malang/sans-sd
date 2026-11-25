<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\HomeroomAssignment;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RaporMengajiSummaryController extends Controller
{
    /**
     * Tampilkan Halaman Rekap & Data Rapor Mengaji SD.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return redirect()->route('login');
        }

        $empId = $currentUser->employee_id;
        $emp = $currentUser->employee;
        $cleanName = $emp ? trim(explode(',', $emp->name)[0]) : '';

        // 1. Inisialisasi Koneksi Database SANS Rapor
        $raporUrl = Setting::get('rapor_url', env('SANS_RAPOR_URL', 'http://sans-rapor.test'));
        $raporDb = Setting::get('rapor_db_name', env('DB_RAPOR_DATABASE', 'sans-rapor'));

        try {
            $baseConfig = config('database.connections.sans_rapor') ?: config('database.connections.mysql');
            $baseConfig['database'] = $raporDb;
            config(['database.connections.sans_rapor' => $baseConfig]);
            DB::purge('sans_rapor');
        } catch (\Throwable $e) {
            // Abaikan jika konfigurasi sudah siap
        }

        // 2. Evaluasi Hak Akses Role
        $isAdmin = $currentUser->isAdmin() || $currentUser->isExecutive() || in_array($currentUser->role, [
            'super_admin', 'superadmin', 'admin_sd', 'admin_smp', 'admin_paud', 'kepala_sekolah', 'waka'
        ]);

        // Koordinator GPQ
        $isKoordinatorGpq = false;
        if ($emp && (stripos($emp->position, 'koordinator gpq') !== false || stripos($emp->position, 'koordinator mengaji') !== false)) {
            $isKoordinatorGpq = true;
        } else {
            try {
                $isKoordinatorGpq = DB::connection('sans_rapor')
                    ->table('unit_coordinators')
                    ->where('unit', 'sd')
                    ->where('type', 'gpq')
                    ->where(function ($q) use ($empId, $cleanName, $emp) {
                        if ($empId) {
                            $q->where('teacher_employee_id', $empId);
                        }
                        if ($cleanName) {
                            $q->orWhere('teacher_name', 'like', "%{$cleanName}%");
                        }
                        if ($emp && !empty($emp->niy) && $emp->niy !== '-') {
                            $q->orWhere('teacher_niy', 'like', "%{$emp->niy}%");
                        }
                    })
                    ->exists();
            } catch (\Throwable $e) {
                $isKoordinatorGpq = false;
            }
        }

        // Wali Kelas
        $homeroomClassroomIds = [];
        if ($empId) {
            $c1 = Classroom::where('homeroom_teacher_id', $empId)->where('is_active', true)->pluck('id')->toArray();
            $c2 = HomeroomAssignment::where('employee_id', $empId)->where('is_active', true)->where('role', 'wali_kelas')->pluck('classroom_id')->toArray();
            $homeroomClassroomIds = array_values(array_unique(array_merge($c1, $c2)));
        }
        $isHomeroom = !empty($homeroomClassroomIds) || $currentUser->isActiveHomeroomTeacher() || $currentUser->role === 'wali_kelas';

        // Guru GPQ & Penugasan Kelompok
        $myGpqAssignments = collect();
        if ($empId || $cleanName) {
            try {
                $myGpqAssignments = DB::connection('sans_rapor')
                    ->table('gpq_group_assignments')
                    ->where('unit', 'sd')
                    ->where(function ($q) use ($empId, $cleanName, $emp) {
                        if ($empId) {
                            $q->where('teacher_employee_id', $empId);
                        }
                        if ($cleanName) {
                            $q->orWhere('teacher_name', 'like', "%{$cleanName}%");
                        }
                        if ($emp && !empty($emp->niy) && $emp->niy !== '-') {
                            $q->orWhere('teacher_niy', 'like', "%{$emp->niy}%");
                        }
                    })
                    ->get();
            } catch (\Throwable $e) {
                $myGpqAssignments = collect();
            }
        }
        $isGpqTeacher = $myGpqAssignments->isNotEmpty() || ($currentUser->isGpqTeacher() ?? false);

        // Validasi Akses Menu
        if (!$isAdmin && !$isKoordinatorGpq && !$isHomeroom && !$isGpqTeacher) {
            abort(403, 'Akses menu Data Rapor Mengaji hanya untuk Administrator, Kepala Sekolah, Koordinator GPQ, Wali Kelas aktif, atau Guru GPQ.');
        }

        // 3. Filter Tahun Ajaran
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        $selectedYearId = (int) $request->get('academic_year_id', $activeAcademicYear?->id);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;

        // 4. Filter Semester (Ganjil / PAS-1 dan Genap / PAT-2)
        $semesters = collect([
            ['id' => 1, 'code' => 'PAS-1', 'db' => 'ganjil', 'name' => 'Semester 1 (Ganjil)', 'badge' => 'Ganjil'],
            ['id' => 2, 'code' => 'PAT-2', 'db' => 'genap', 'name' => 'Semester 2 (Genap)', 'badge' => 'Genap'],
        ]);

        $rawSemester = $request->get('semester');
        if (!$rawSemester) {
            $activeSemCode = strtolower((string)($activeAcademicYear?->semester ?? 'ganjil'));
            $rawSemester = in_array($activeSemCode, ['genap', 'pat-2', '2']) ? 'PAT-2' : 'PAS-1';
        }

        if (in_array(strtolower($rawSemester), ['genap', 'pat-2', 'pat_2', 'pts-2', '2'])) {
            $selectedSemester = 'PAT-2';
            $dbSemester = 'genap';
        } else {
            $selectedSemester = 'PAS-1';
            $dbSemester = 'ganjil';
        }

        // 5. Query Master Kelompok Mengaji & Shift Aktif dari DB sans_rapor
        $allGroups = collect();
        $allShifts = collect();
        try {
            $allGroups = DB::connection('sans_rapor')
                ->table('religious_student_groups')
                ->where('unit', 'sd')
                ->where('academic_year_id', $selectedYearId)
                ->where('semester', $dbSemester)
                ->whereNotNull('group_name')
                ->where('group_name', '!=', '')
                ->distinct()
                ->orderBy('group_name')
                ->pluck('group_name');

            if ($allGroups->isEmpty()) {
                // Fallback dari gpq_group_assignments
                $allGroups = DB::connection('sans_rapor')
                    ->table('gpq_group_assignments')
                    ->where('unit', 'sd')
                    ->where('academic_year_id', $selectedYearId)
                    ->where('semester', $dbSemester)
                    ->distinct()
                    ->orderBy('group_name')
                    ->pluck('group_name');
            }

            $allShifts = DB::connection('sans_rapor')
                ->table('religious_student_groups')
                ->where('unit', 'sd')
                ->where('academic_year_id', $selectedYearId)
                ->where('semester', $dbSemester)
                ->whereNotNull('shift')
                ->where('shift', '!=', '')
                ->distinct()
                ->orderBy('shift')
                ->pluck('shift');

            if ($allShifts->isEmpty()) {
                $allShifts = collect(['Shift 1', 'Shift 2', 'Shift 3']);
            }
        } catch (\Throwable $e) {
            // Jika DB sans_rapor belum ada data
            $allGroups = collect(['Kelompok 1', 'Kelompok 2', 'Kelompok 3']);
            $allShifts = collect(['Shift 1', 'Shift 2', 'Shift 3']);
        }

        // 6. Query Daftar Rombel (Classrooms)
        $classroomsQuery = Classroom::where('is_active', true)->with('classLevel');
        if ($selectedYearId) {
            $classroomsQuery->where('academic_year_id', $selectedYearId);
        }

        $isStrictHomeroom = $isHomeroom && !$isAdmin && !$isKoordinatorGpq && !$isGpqTeacher;
        if ($isStrictHomeroom) {
            $classroomsQuery->whereIn('id', $homeroomClassroomIds);
        }

        $classrooms = $classroomsQuery->get()->sortBy(function ($c) {
            $order = str_pad((string) ($c->classLevel?->order ?? 99), 2, '0', STR_PAD_LEFT);
            $code = $c->code ?? $c->name ?? '';
            return "{$order}_{$code}";
        })->values();

        // 7. Pengambilan Filter Request
        $selectedClassroomId = $request->get('classroom_id');
        if ($isStrictHomeroom && !empty($homeroomClassroomIds)) {
            if (!$selectedClassroomId || !in_array((int)$selectedClassroomId, $homeroomClassroomIds)) {
                $selectedClassroomId = (string) $homeroomClassroomIds[0];
            }
        }

        $selectedGroup = $request->get('group_name');
        $selectedShift = $request->get('shift');
        $selectedStatus = $request->get('status');
        $search = trim((string) $request->get('search', ''));

        // Jika Guru GPQ Murni (bukan admin, bukan kepsek/koordinator, bukan wali kelas), batasi ke kelompok miliknya
        $isStrictGpq = $isGpqTeacher && !$isAdmin && !$isKoordinatorGpq && !$isHomeroom;
        $myGroupNames = $myGpqAssignments->pluck('group_name')->unique()->toArray();
        $myShiftNames = $myGpqAssignments->pluck('shift')->unique()->toArray();

        // 8. Query Data Siswa SD
        $studentsQuery = Student::where('status', 'aktif')
            ->with(['classroom.classLevel']);

        if ($selectedClassroomId) {
            $studentsQuery->where('classroom_id', $selectedClassroomId);
        } elseif ($isStrictHomeroom && !empty($homeroomClassroomIds)) {
            $studentsQuery->whereIn('classroom_id', $homeroomClassroomIds);
        }

        if (!empty($search)) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->orderBy('full_name')->get();
        $studentIds = $students->pluck('id')->toArray();

        // 9. Ambil Data Mengaji dari Database sans_rapor
        $religiousGroups = collect();
        $religiousAssessments = collect();
        $gpqAssignments = collect();

        try {
            if (!empty($studentIds)) {
                $religiousGroups = DB::connection('sans_rapor')
                    ->table('religious_student_groups')
                    ->where('unit', 'sd')
                    ->where('academic_year_id', $selectedYearId)
                    ->where('semester', $dbSemester)
                    ->whereIn('student_id', $studentIds)
                    ->get()
                    ->keyBy('student_id');

                $religiousAssessments = DB::connection('sans_rapor')
                    ->table('religious_assessments')
                    ->where('unit', 'sd')
                    ->where('academic_year_id', $selectedYearId)
                    ->where('semester', $dbSemester)
                    ->whereIn('student_id', $studentIds)
                    ->get()
                    ->keyBy('student_id');
            }

            $gpqAssignments = DB::connection('sans_rapor')
                ->table('gpq_group_assignments')
                ->where('unit', 'sd')
                ->where('academic_year_id', $selectedYearId)
                ->where('semester', $dbSemester)
                ->get();
        } catch (\Throwable $e) {
            // Fallback gracefully jika ada kendala koneksi
        }

        // Map Guru GPQ per Group & Shift
        $gpqTeacherMap = [];
        foreach ($gpqAssignments as $asgn) {
            $key = trim($asgn->group_name) . '|' . trim($asgn->shift);
            $gpqTeacherMap[$key] = [
                'teacher_name' => $asgn->teacher_name,
                'teacher_niy' => $asgn->teacher_niy,
                'location' => $asgn->location,
            ];
        }

        // 10. Gabungkan & Evaluasi Data Tiap Siswa
        $enrichedStudents = collect();
        $countSiapCetak = 0;
        $countBelumLengkap = 0;
        $countBelumDiisi = 0;

        foreach ($students as $student) {
            $group = $religiousGroups->get($student->id);
            $assessment = $religiousAssessments->get($student->id);

            $groupName = $group?->group_name ?: null;
            $shift = $group?->shift ?: null;
            $location = $group?->location ?: null;
            $statusStudent = $group?->status ?: 'Reguler';
            $activeJilid = $group?->active_jilid ?: ($assessment?->jilid ?: '-');

            // Guru GPQ Pengampu
            $teacherKey = trim((string)$groupName) . '|' . trim((string)$shift);
            $assignedTeacher = $gpqTeacherMap[$teacherKey] ?? null;
            $teacherName = $group?->gpq_teacher_name ?: ($assignedTeacher['teacher_name'] ?? '-');
            if (empty($location) && !empty($assignedTeacher['location'])) {
                $location = $assignedTeacher['location'];
            }

            // Catatan
            $notesGpq = !empty($assessment?->notes_gpq) ? trim($assessment->notes_gpq) : null;
            $notesPai = !empty($assessment?->notes_pai) ? trim($assessment->notes_pai) : null;

            // Penilaian
            $hasGpqNotes = !empty($notesGpq);
            $hasPaiNotes = !empty($notesPai);
            $hasScores = !empty($assessment?->score_tajwid) 
                || !empty($assessment?->score_fashahah) 
                || !empty($assessment?->score_lagu) 
                || (!empty($assessment?->tahfizh_scores) && $assessment->tahfizh_scores !== 'null');

            // Evaluasi Status
            if ($hasGpqNotes && $hasPaiNotes) {
                $statusRapor = 'siap_cetak';
                $statusLabel = 'Siap Cetak';
                $countSiapCetak++;
            } elseif ($hasGpqNotes || $hasPaiNotes || $hasScores || (!empty($assessment?->jilid) && $assessment->jilid !== '-')) {
                $statusRapor = 'belum_lengkap';
                $statusLabel = 'Belum Lengkap';
                $countBelumLengkap++;
            } else {
                $statusRapor = 'belum_diisi';
                $statusLabel = 'Belum Diisi';
                $countBelumDiisi++;
            }

            // Filter Scoping Guru GPQ Murni
            if ($isStrictGpq) {
                $isMyStudent = false;
                if ($groupName && in_array($groupName, $myGroupNames)) {
                    if (empty($myShiftNames) || in_array($shift, $myShiftNames)) {
                        $isMyStudent = true;
                    }
                }
                if (!$isMyStudent) {
                    continue; // Skip siswa yang bukan di kelompok binaan guru GPQ ini
                }
            }

            // Filter Dropdown Kelompok
            if (!empty($selectedGroup) && $groupName !== $selectedGroup) {
                continue;
            }

            // Filter Dropdown Shift
            if (!empty($selectedShift) && $shift !== $selectedShift) {
                continue;
            }

            // Filter Dropdown Status
            if (!empty($selectedStatus) && $statusRapor !== $selectedStatus) {
                continue;
            }

            // URL Cetak PDF Rapor Mengaji
            $printUrl = "{$raporUrl}/mengaji/reports/print/{$student->id}?unit=sd&academic_year_id={$selectedYearId}&semester={$dbSemester}";

            $enrichedStudents->push((object)[
                'id' => $student->id,
                'full_name' => $student->full_name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'classroom_name' => $student->classroom?->name ?? '-',
                'classroom_code' => $student->classroom?->code ?? '-',
                'group_name' => $groupName ?: 'Belum Masuk Kelompok',
                'shift' => $shift ?: '-',
                'location' => $location ?: '-',
                'status_student' => $statusStudent,
                'active_jilid' => $activeJilid,
                'gpq_teacher_name' => $teacherName,
                'notes_gpq' => $notesGpq,
                'notes_pai' => $notesPai,
                'status_rapor' => $statusRapor,
                'status_label' => $statusLabel,
                'print_url' => $printUrl,
            ]);
        }

        // 11. Pagination
        $perPage = 30;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $enrichedStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedStudents = new LengthAwarePaginator(
            $pagedItems,
            $enrichedStudents->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // URL Cetak Rombel
        $printClassroomUrl = null;
        if (!empty($selectedClassroomId)) {
            $printClassroomUrl = "{$raporUrl}/mengaji/reports/print-classroom/{$selectedClassroomId}?unit=sd&academic_year_id={$selectedYearId}&semester={$dbSemester}";
        }

        return view('admin.reports.rapor_mengaji_summary', [
            'paginatedStudents' => $paginatedStudents,
            'academicYears' => $academicYears,
            'selectedYearId' => $selectedYearId,
            'selectedYear' => $selectedYear,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'dbSemester' => $dbSemester,
            'classrooms' => $classrooms,
            'selectedClassroomId' => $selectedClassroomId,
            'allGroups' => $allGroups,
            'selectedGroup' => $selectedGroup,
            'allShifts' => $allShifts,
            'selectedShift' => $selectedShift,
            'selectedStatus' => $selectedStatus,
            'search' => $search,
            'totalStudents' => $students->count(),
            'totalFiltered' => $enrichedStudents->count(),
            'totalSiapCetak' => $countSiapCetak,
            'totalBelumLengkap' => $countBelumLengkap,
            'totalBelumDiisi' => $countBelumDiisi,
            'printClassroomUrl' => $printClassroomUrl,
            'isAdmin' => $isAdmin,
            'isKoordinatorGpq' => $isKoordinatorGpq,
            'isHomeroom' => $isHomeroom,
            'isGpqTeacher' => $isGpqTeacher,
            'raporUrl' => $raporUrl,
        ]);
    }
}
