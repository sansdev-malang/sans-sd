<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentReportController extends Controller
{
    /**
     * Display the Student & Classroom Distribution Report (Rekapitulasi Rombel & Kesiswaan).
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Selected year ID or active year ID
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $selectedYearId ? [$selectedYearId] : [];

        $reportData = $this->calculateReportData($matchingYearIds);

        return view('admin.reports.students.index', array_merge($reportData, [
            'academicYears' => $academicYears,
            'selectedYear' => $selectedYear,
            'selectedYearId' => $selectedYear?->id,
            'selectedYearName' => $selectedYearName,
        ]));
    }

    /**
     * Print-friendly view of the report.
     */
    public function print(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id')->toArray();

        $reportData = $this->calculateReportData($matchingYearIds);

        $headmasterEmployee = \App\Models\Employee::where('position', 'like', '%Kepala Sekolah%')->first();
        $headmasterName = $headmasterEmployee?->name ?? 'Andreas Setiyono, S.Pd.Gr., M.Kom.';
        $headmasterNiy = $headmasterEmployee?->niy ?? $headmasterEmployee?->nuptk ?? null;

        return view('admin.reports.students.print', array_merge($reportData, [
            'selectedYear' => $selectedYear,
            'selectedYearId' => $selectedYear?->id,
            'selectedYearName' => $selectedYearName,
            'headmasterName' => $headmasterName,
            'headmasterNiy' => $headmasterNiy,
        ]));
    }

    /**
     * Export the report to Excel (.xlsx) matching official TU Sheet 2 structure.
     */
    public function export(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id')->toArray();

        $reportData = $this->calculateReportData($matchingYearIds);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT');

        // Document Title
        $unitName = function_exists('setting') ? setting('unit_name', 'SD Anak Saleh') : 'SD Anak Saleh';
        $yearName = $selectedYear ? $selectedYear->name : 'Semua Tahun';

        $sheet->setCellValue('A1', "Data Peserta Didik " . $unitName);
        $sheet->setCellValue('A2', "Tapel {$yearName}");
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);

        // Table Header
        $headers = [
            'A4' => 'No',
            'B4' => 'Nama Kelas',
            'C4' => 'Kode Rombel',
            'D4' => 'LAKI-LAKI',
            'E4' => 'PEREMPUAN',
            'F4' => 'Jml Siswa @ Kelas',
            'G4' => 'INKLUSI',
            'H4' => 'WALI KELAS',
            'I4' => 'GURU KELAS',
            'J4' => 'GPK',
            'K4' => 'GPQ',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A4:K4')->applyFromArray($headerStyle);

        $rowNum = 5;
        $no = 1;

        foreach ($reportData['levelReports'] as $levelData) {
            if (empty($levelData['classrooms'])) {
                continue;
            }

            // Level Header Row
            $coordText = strtoupper($levelData['level']->name);
            if (!empty($levelData['coordinator'])) {
                $coordText .= ' (Koordinator: ' . $levelData['coordinator'] . ')';
            }
            $sheet->setCellValue("A{$rowNum}", $coordText);
            $sheet->mergeCells("A{$rowNum}:K{$rowNum}");
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '334155']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $rowNum++;

            // Classroom Rows
            foreach ($levelData['classrooms'] as $cr) {
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $cr['name']);
                $sheet->setCellValue("C{$rowNum}", $cr['code']);
                $sheet->setCellValue("D{$rowNum}", $cr['male']);
                $sheet->setCellValue("E{$rowNum}", $cr['female']);
                $sheet->setCellValue("F{$rowNum}", $cr['total']);
                $sheet->setCellValue("G{$rowNum}", $cr['pdbk']);
                $sheet->setCellValue("H{$rowNum}", $cr['homeroom_teacher'] ?: '-');
                $sheet->setCellValue("I{$rowNum}", $cr['class_teacher'] ?: '-');
                $sheet->setCellValue("J{$rowNum}", !empty($cr['gpk_teachers']) ? implode(', ', $cr['gpk_teachers']) : '-');
                $sheet->setCellValue("K{$rowNum}", $cr['gpq'] ?: '-');

                $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                ]);
                $sheet->getStyle("A{$rowNum}:C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $rowNum++;
            }

            // Subtotal Level Row
            $sheet->setCellValue("A{$rowNum}", "Jml Siswa @ " . $levelData['level']->name);
            $sheet->mergeCells("A{$rowNum}:C{$rowNum}");
            $sheet->setCellValue("D{$rowNum}", $levelData['subtotal_male']);
            $sheet->setCellValue("E{$rowNum}", $levelData['subtotal_female']);
            $sheet->setCellValue("F{$rowNum}", $levelData['subtotal_students']);
            $sheet->setCellValue("G{$rowNum}", $levelData['subtotal_pdbk']);
            $sheet->setCellValue("H{$rowNum}", '');
            $sheet->setCellValue("I{$rowNum}", '');
            $sheet->setCellValue("J{$rowNum}", '');
            $sheet->setCellValue("K{$rowNum}", '');

            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
            ]);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // 4. Unassigned Students (Siswa Aktif Belum Masuk Rombel)
        if (!empty($reportData['unassignedStudents']) && $reportData['unassignedTotal'] > 0) {
            $sheet->setCellValue("A{$rowNum}", "SISWA AKTIF BELUM MASUK ROMBEL (PERLU ALOKASI KELAS)");
            $sheet->mergeCells("A{$rowNum}:K{$rowNum}");
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '92400E']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FCD34D']]],
            ]);
            $rowNum++;

            foreach ($reportData['unassignedStudents'] as $us) {
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $us->full_name . ' (NIS: ' . $us->nis . ')');
                $sheet->setCellValue("C{$rowNum}", 'Tanpa Rombel');
                $sheet->setCellValue("D{$rowNum}", in_array($us->gender, ['L', 'Laki-laki', 'Male', 'LAKI-LAKI']) ? 1 : 0);
                $sheet->setCellValue("E{$rowNum}", in_array($us->gender, ['P', 'Perempuan', 'Female', 'PEREMPUAN']) ? 1 : 0);
                $sheet->setCellValue("F{$rowNum}", 1);
                $isPdbk = ($us->student_type && (str_contains(strtoupper($us->student_type), 'PDBK') || str_contains(strtoupper($us->student_type), 'KHUSUS') || str_contains(strtoupper($us->student_type), 'INKLUSI'))) || !empty($us->special_needs_type) || !empty($us->gpk_employee_id);
                $sheet->setCellValue("G{$rowNum}", $isPdbk ? 1 : 0);
                $sheet->setCellValue("H{$rowNum}", '-');
                $sheet->setCellValue("I{$rowNum}", '-');
                $sheet->setCellValue("J{$rowNum}", '-');
                $sheet->setCellValue("K{$rowNum}", '-');

                $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                ]);
                $sheet->getStyle("A{$rowNum}:C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $rowNum++;
            }
        }

        // Grand Total Row
        $sheet->setCellValue("A{$rowNum}", "JUMLAH PESERTA DIDIK KESELURUHAN:");
        $sheet->mergeCells("A{$rowNum}:C{$rowNum}");
        $sheet->setCellValue("D{$rowNum}", $reportData['grandTotalMale']);
        $sheet->setCellValue("E{$rowNum}", $reportData['grandTotalFemale']);
        $sheet->setCellValue("F{$rowNum}", $reportData['grandTotalStudents']);
        $sheet->setCellValue("G{$rowNum}", $reportData['grandTotalPdbk']);
        $sheet->setCellValue("H{$rowNum}", '');
        $sheet->setCellValue("I{$rowNum}", '');
        $sheet->setCellValue("J{$rowNum}", '');
        $sheet->setCellValue("K{$rowNum}", '');

        $sheet->getStyle("A{$rowNum}:K{$rowNum}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4338CA']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '312E81']]],
        ]);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekapitulasi_Peserta_Didik_' . str_replace(['/', ' '], '_', $yearName) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Calculate all report structures and aggregates.
     */
    private function calculateReportData(array $matchingYearIds = []): array
    {
        $classLevels = ClassLevel::orderBy('order', 'asc')->orderBy('id', 'asc')->with(['classrooms' => function($q) use ($matchingYearIds) {
            if (!empty($matchingYearIds)) {
                $q->whereIn('academic_year_id', $matchingYearIds);
            }
            $q->where('is_active', true)->orderBy('code', 'asc')->orderBy('name', 'asc')->with(['homeroomTeacher', 'students' => function($sq) use ($matchingYearIds) {
                $sq->where('status', 'aktif');
                if (!empty($matchingYearIds)) {
                    $sq->whereIn('academic_year_id', $matchingYearIds);
                }
                $sq->with('gpkTeacher');
            }]);
        }])->get();

        $levelReports = [];
        $grandTotalMale = 0;
        $grandTotalFemale = 0;
        $grandTotalStudents = 0;
        $grandTotalPdbk = 0;
        $grandTotalClassrooms = 0;

        $staffMatrix = $this->getStaffMatrixReport();

        // Load dynamic assignments from database per selected academic year
        $dbAssignments = \App\Models\HomeroomAssignment::with('employee')
            ->when(!empty($matchingYearIds), function($q) use ($matchingYearIds) {
                $q->whereIn('academic_year_id', $matchingYearIds);
            })
            ->where('is_active', true)
            ->get();

        foreach ($classLevels as $level) {
            $classroomsData = [];
            $subMale = 0;
            $subFemale = 0;
            $subTotal = 0;
            $subPdbk = 0;
            $levelNum = $level->order ?: (int) filter_var($level->name, FILTER_SANITIZE_NUMBER_INT);
            $levelStaffInfo = $staffMatrix[$levelNum] ?? null;

            // Coordinator & Assistants for this level
            $levelAssignments = $dbAssignments->where('class_level_id', $level->id);
            $dbCoordinator = $levelAssignments->where('role', 'koordinator_tingkat')->first()?->employee?->name;
            $dbAssistants = $levelAssignments->where('role', 'pendamping_tingkat')->pluck('employee.name')->filter()->values()->all();

            $coordinatorName = $dbCoordinator ?: ($levelStaffInfo['coordinator'] ?? null);
            $assistantsList = !empty($dbAssistants) ? $dbAssistants : ($levelStaffInfo['assistants'] ?? []);

            foreach ($level->classrooms as $cr) {
                $male = $cr->students->whereIn('gender', ['L', 'Laki-laki', 'Male', 'LAKI-LAKI'])->count();
                $female = $cr->students->whereIn('gender', ['P', 'Perempuan', 'Female', 'PEREMPUAN'])->count();
                $total = $cr->students->count();
                $pdbk = $cr->students->filter(function($s) {
                    return ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS') || str_contains(strtoupper($s->student_type), 'INKLUSI'))) || !empty($s->special_needs_type) || !empty($s->gpk_employee_id);
                })->count();

                $gpkTeachers = $cr->students->whereNotNull('gpkTeacher')->pluck('gpkTeacher.name')->unique()->values()->all();

                // Matched staff data from database assignments or Sheet Report TU fallback
                $staffCode = strtoupper(trim($cr->code ?: ''));
                $matchedStaff = $levelStaffInfo['classes'][$staffCode] ?? null;

                $crAssignments = $dbAssignments->where('classroom_id', $cr->id);
                $dbHomeroom = $crAssignments->where('role', 'wali_kelas')->first()?->employee?->name;
                $dbClassTeacher = $crAssignments->where('role', 'guru_kelas')->first()?->employee?->name;
                $dbGpk = $crAssignments->where('role', 'gpk')->pluck('employee.name')->filter()->values()->all();
                $dbGpq = $crAssignments->where('role', 'gpq')->first()?->employee?->name;

                $homeroomTeacher = $dbHomeroom ?: ($cr->homeroomTeacher?->name ?: ($matchedStaff['homeroom'] ?? '-'));
                $classTeacher = $dbClassTeacher ?: ($matchedStaff['class_teacher'] ?? '-');
                $gpkList = !empty($dbGpk) ? $dbGpk : (!empty($matchedStaff['gpk']) ? $matchedStaff['gpk'] : (!empty($gpkTeachers) ? $gpkTeachers : []));
                $gpq = $dbGpq ?: ($matchedStaff['gpq'] ?? '-');

                $classroomsData[] = [
                    'id' => $cr->id,
                    'name' => $cr->name,
                    'code' => $cr->code ?: '-',
                    'full_name' => $cr->full_name,
                    'capacity' => $cr->capacity ?: 30,
                    'homeroom_teacher' => $homeroomTeacher,
                    'homeroom_teacher_title' => $cr->homeroomTeacher?->position ?: 'Wali Kelas',
                    'class_teacher' => $classTeacher,
                    'gpk_teachers' => $gpkList,
                    'gpq' => $gpq,
                    'male' => $male,
                    'female' => $female,
                    'total' => $total,
                    'pdbk' => $pdbk,
                ];

                $subMale += $male;
                $subFemale += $female;
                $subTotal += $total;
                $subPdbk += $pdbk;
                $grandTotalClassrooms++;
            }

            $levelReports[] = [
                'level' => $level,
                'coordinator' => $coordinatorName,
                'assistants' => $assistantsList,
                'classrooms' => $classroomsData,
                'subtotal_male' => $subMale,
                'subtotal_female' => $subFemale,
                'subtotal_students' => $subTotal,
                'subtotal_pdbk' => $subPdbk,
            ];

            $grandTotalMale += $subMale;
            $grandTotalFemale += $subFemale;
            $grandTotalStudents += $subTotal;
            $grandTotalPdbk += $subPdbk;
        }

        // Siswa Aktif yang Belum Masuk Rombel / Tanpa Kelas
        $unassignedQuery = Student::where('status', 'aktif')->whereNull('classroom_id');
        if (!empty($matchingYearIds)) {
            $unassignedQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $unassignedStudents = $unassignedQuery->get();
        $unassignedMale = $unassignedStudents->whereIn('gender', ['L', 'Laki-laki', 'Male', 'LAKI-LAKI'])->count();
        $unassignedFemale = $unassignedStudents->whereIn('gender', ['P', 'Perempuan', 'Female', 'PEREMPUAN'])->count();
        $unassignedTotal = $unassignedStudents->count();
        $unassignedPdbk = $unassignedStudents->filter(function($s) {
            return ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS') || str_contains(strtoupper($s->student_type), 'INKLUSI'))) || !empty($s->special_needs_type) || !empty($s->gpk_employee_id);
        })->count();

        $grandTotalMale += $unassignedMale;
        $grandTotalFemale += $unassignedFemale;
        $grandTotalStudents += $unassignedTotal;
        $grandTotalPdbk += $unassignedPdbk;

        $malePercent = $grandTotalStudents > 0 ? round(($grandTotalMale / $grandTotalStudents) * 100, 1) : 0;
        $femalePercent = $grandTotalStudents > 0 ? round(($grandTotalFemale / $grandTotalStudents) * 100, 1) : 0;
        $pdbkPercent = $grandTotalStudents > 0 ? round(($grandTotalPdbk / $grandTotalStudents) * 100, 1) : 0;

        return [
            'levelReports' => $levelReports,
            'unassignedStudents' => $unassignedStudents,
            'unassignedMale' => $unassignedMale,
            'unassignedFemale' => $unassignedFemale,
            'unassignedTotal' => $unassignedTotal,
            'unassignedPdbk' => $unassignedPdbk,
            'grandTotalMale' => $grandTotalMale,
            'grandTotalFemale' => $grandTotalFemale,
            'grandTotalStudents' => $grandTotalStudents,
            'grandTotalPdbk' => $grandTotalPdbk,
            'grandTotalClassrooms' => $grandTotalClassrooms,
            'malePercent' => $malePercent,
            'femalePercent' => $femalePercent,
            'pdbkPercent' => $pdbkPercent,
        ];
    }

    /**
     * Complete teacher and staff assignments matrix from Sheet REPORT TU.
     */
    private function getStaffMatrixReport(): array
    {
        return [
            1 => [
                'coordinator' => 'Romadhoniar Fitri Aini, S.Pd.I.',
                'assistants' => ['Selvi Dwi Wahyuni, M.Pd', 'Mohammad Nurahman, S.P.I., M.Pd'],
                'classes' => [
                    '1A' => [
                        'name' => 'BERLIAN', 'code' => '1A',
                        'homeroom' => 'Nadia Fatma Yanti, S.Pd',
                        'class_teacher' => 'Ari Iswahyudi, S.Psi',
                        'gpk' => ['Mu Ida Nur Fahilah, S.Pd -- Iinmartalivia', 'New GPK Kolaborasi'],
                        'gpq' => '-',
                    ],
                    '1B' => [
                        'name' => 'MUTIARA', 'code' => '1B',
                        'homeroom' => 'Dini Eko Wulandari, S.Psi',
                        'class_teacher' => 'Milatun Nafisah, S.Psi',
                        'gpk' => ['Arik Wijayanto, S.Psi', 'Yetsky Yudistira, S.Or'],
                        'gpq' => 'Azzahro Maulidiah',
                    ],
                    '1C' => [
                        'name' => 'SAFIR', 'code' => '1C',
                        'homeroom' => 'Gita Noviria, S.Pd',
                        'class_teacher' => 'Varianta Jawa Yuam Miranda, S.Pd.Gr',
                        'gpk' => ['Ika Puspitasari, S.Psi', 'Ferdivan Andra Farerra'],
                        'gpq' => 'Putri Wahyuning Laili',
                    ],
                    '1D' => [
                        'name' => 'RUBY', 'code' => '1D',
                        'homeroom' => 'Irma Wahyu Putri Yoditya, S.Pd',
                        'class_teacher' => 'Suwaibatul Aslamiyah, A.Md.Keb., S.Pd',
                        'gpk' => ['Raga Cahya Taufikurrahman, S.Pd', 'GPK Kolaborasi - Bu Asri'],
                        'gpq' => '-',
                    ],
                ]
            ],
            2 => [
                'coordinator' => 'Ika Wijayanti, S.AB., S.Pd',
                'assistants' => [],
                'classes' => [
                    '2A' => [
                        'name' => 'GIOK', 'code' => '2A',
                        'homeroom' => 'Irdayus Melindra, S.Pd',
                        'class_teacher' => '-',
                        'gpk' => ['Rikha Dwi Rachmawati, S.Psi', 'Sukmawati Megawijayanti Susanto, S.Psi', 'GPK Kolaborasi - Bu Valda'],
                        'gpq' => 'Aisyah Ani Rosita, S.E',
                    ],
                    '2B' => [
                        'name' => 'PIRUS', 'code' => '2B',
                        'homeroom' => 'Miftakhul Jannah, S.Pd., S.Pd.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Nurhayati', 'Sherly Annisa Ramadhani'],
                        'gpq' => '-',
                    ],
                    '2C' => [
                        'name' => 'AMETHYST', 'code' => '2C',
                        'homeroom' => 'Aning Masyrufatin Furoida, S.Pd.I., S.Pd.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Nur Laili Sa\'adah, S.A'],
                        'gpq' => '-',
                    ],
                    '2D' => [
                        'name' => 'OPAL', 'code' => '2D',
                        'homeroom' => 'Anis Amelia, S.Pd',
                        'class_teacher' => '-',
                        'gpk' => ['Akhmad Saiful, S.Pd', 'GPK Kolaborasi - Bu Ayu'],
                        'gpq' => 'Rr. Nur Apriyanti Atika Anggraini Soeharto, M.Pd',
                    ],
                ]
            ],
            3 => [
                'coordinator' => 'Paramita Puri Anggraini, S.Pd.Gr & Prima Suci Ibar Wati Ningrum, S.Or., M.Pd',
                'assistants' => [],
                'classes' => [
                    '3A' => [
                        'name' => 'TOPAZ', 'code' => '3A',
                        'homeroom' => 'Ika Su\'udia, S.Si.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Nurul Asri Fitriyah'],
                        'gpq' => 'Sukma Abdul Rozy',
                    ],
                    '3B' => [
                        'name' => 'AQUAMARINE', 'code' => '3B',
                        'homeroom' => 'Hj. Noor Jeehan, S.Ag., M.Pd.I',
                        'class_teacher' => '-',
                        'gpk' => ['Desilfa Dwi Nursavitri, S.Psi'],
                        'gpq' => 'Hurul Jinani, S.Pd',
                    ],
                    '3C' => [
                        'name' => 'OBSIDIAN', 'code' => '3C',
                        'homeroom' => 'Arif Nur Rahman, S.S., M.Pd.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Afriska Nur Azizah, S.Pd'],
                        'gpq' => '-',
                    ],
                    '3D' => [
                        'name' => 'ZAMRUD', 'code' => '3D',
                        'homeroom' => 'Desty Ariani Mutiara, S.Pd., S.Pd',
                        'class_teacher' => '-',
                        'gpk' => ['M. Baha\'ul Alamsyah Al Faini, S.Pd'],
                        'gpq' => '-',
                    ],
                ]
            ],
            4 => [
                'coordinator' => 'Nur Risky Marsa Romadhona, S.Pd., S.Pd.Gr',
                'assistants' => [],
                'classes' => [
                    '4A' => [
                        'name' => 'JASPER', 'code' => '4A',
                        'homeroom' => 'Miftakul Jannah, S.Pd',
                        'class_teacher' => 'Risas Wahyudi, S.Pd.',
                        'gpk' => [],
                        'gpq' => 'Muhammad Akbar Amin, S.Pd.Gr',
                    ],
                    '4B' => [
                        'name' => 'MALASIT', 'code' => '4B',
                        'homeroom' => 'Ucik Sriwahyuni, S.Pd',
                        'class_teacher' => 'Masruhan, S.Pd.I., M.Pd',
                        'gpk' => ['Kofifah Indar Khoiroh, S.Psi'],
                        'gpq' => '-',
                    ],
                    '4C' => [
                        'name' => 'GARNET', 'code' => '4C',
                        'homeroom' => 'Sri Subakti, S.Pd.SD.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Nila Fadilah, S.Pd'],
                        'gpq' => 'Yusuf Mu\'awiyah, S.TP',
                    ],
                    '4D' => [
                        'name' => 'CITRINE', 'code' => '4D',
                        'homeroom' => 'Lailatul Munawaroh, S.Pd., M.Pd',
                        'class_teacher' => '-',
                        'gpk' => [],
                        'gpq' => 'Nabila Ucinanda Betarizqy, S.Pd',
                    ],
                ]
            ],
            5 => [
                'coordinator' => 'Kusnia, S.Pd.I & Mokh. Danny Permata Utomo, S.Pd.Gr',
                'assistants' => [],
                'classes' => [
                    '5A' => [
                        'name' => 'BACAN', 'code' => '5A',
                        'homeroom' => 'Ghoniyur Rohman, S.Pd., S.Pd.SD.Gr',
                        'class_teacher' => '-',
                        'gpk' => ['Tursina Ainun Nisa\' Caniago, S.Sos'],
                        'gpq' => 'Zulaihah, S.Pd',
                    ],
                    '5B' => [
                        'name' => 'TOURMALINE', 'code' => '5B',
                        'homeroom' => 'Anida Nafis Qotrunnada, S.Pd',
                        'class_teacher' => '-',
                        'gpk' => [],
                        'gpq' => 'Muhammad Wildan Makhasin',
                    ],
                    '5C' => [
                        'name' => 'PERMATA', 'code' => '5C',
                        'homeroom' => 'Moch. Yusroni, S.Pd.Gr',
                        'class_teacher' => 'Binti Zakkiyatul Faqiroh, S.Pd',
                        'gpk' => [],
                        'gpq' => '-',
                    ],
                    '5D' => [
                        'name' => 'AGATE', 'code' => '5D',
                        'homeroom' => 'Desi Ratnasari, S.Pd',
                        'class_teacher' => 'Achmad Efendi, S.Hum',
                        'gpk' => ['Venorica Afdela, S.Psi'],
                        'gpq' => 'Shofiyatul Mardiyah',
                    ],
                ]
            ],
            6 => [
                'coordinator' => 'Nihayatul Hasanah, S.Pd., S.Pd',
                'assistants' => [],
                'classes' => [
                    '6A' => [
                        'name' => 'EMERALD', 'code' => '6A',
                        'homeroom' => 'Dara Eges Nuryana, S.Pd',
                        'class_teacher' => '-',
                        'gpk' => ['Yahya Firmansyah, S.Pd'],
                        'gpq' => 'Rima Rahmayanti, S.Pd',
                    ],
                    '6B' => [
                        'name' => 'ALEXANDRITE', 'code' => '6B',
                        'homeroom' => 'Ainur Rifqi, M.Pd',
                        'class_teacher' => '-',
                        'gpk' => ['Ahmad Shobirin, S.Pd'],
                        'gpq' => '-',
                    ],
                    '6C' => [
                        'name' => 'ONIKS', 'code' => '6C',
                        'homeroom' => 'Puri Wiranti, S.Pd',
                        'class_teacher' => 'Jaronah, S.Pd',
                        'gpk' => ['Syaifud Dina Fitriana'],
                        'gpq' => 'Nurul Akhyar, M.Pd',
                    ],
                    '6D' => [
                        'name' => 'KUARSA', 'code' => '6D',
                        'homeroom' => 'Hj. Sri Yudianti, S.Pd',
                        'class_teacher' => '-',
                        'gpk' => [],
                        'gpq' => 'M. Mukid A.P.',
                    ],
                ]
            ],
        ];
    }
}
