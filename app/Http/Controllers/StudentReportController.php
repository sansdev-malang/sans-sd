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
        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Unique yearly academic years for annual entities (Tahunan)
        $uniqueAcademicYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Selected year ID or active year ID
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id')->toArray();

        $reportData = $this->calculateReportData($matchingYearIds);

        return view('admin.reports.students.index', array_merge($reportData, [
            'academicYears' => $uniqueAcademicYears,
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
            'G4' => 'INKLUSI / PDBK',
            'H4' => 'WALI KELAS',
            'I4' => 'GURU PENDAMPING KHUSUS (GPK)',
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
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);

        $rowNum = 5;
        $no = 1;

        foreach ($reportData['levelReports'] as $levelData) {
            if (empty($levelData['classrooms'])) {
                continue;
            }

            // Level Header Row
            $sheet->setCellValue("A{$rowNum}", strtoupper($levelData['level']->name));
            $sheet->mergeCells("A{$rowNum}:I{$rowNum}");
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
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
                $sheet->setCellValue("I{$rowNum}", !empty($cr['gpk_teachers']) ? implode(', ', $cr['gpk_teachers']) : '-');

                $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
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

            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
            ]);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
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

        $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4338CA']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '312E81']]],
        ]);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
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
            $q->where('is_active', true)->orderBy('name', 'asc')->with(['homeroomTeacher', 'students' => function($sq) use ($matchingYearIds) {
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

        foreach ($classLevels as $level) {
            $classroomsData = [];
            $subMale = 0;
            $subFemale = 0;
            $subTotal = 0;
            $subPdbk = 0;

            foreach ($level->classrooms as $cr) {
                $male = $cr->students->whereIn('gender', ['L', 'Laki-laki', 'Male', 'LAKI-LAKI'])->count();
                $female = $cr->students->whereIn('gender', ['P', 'Perempuan', 'Female', 'PEREMPUAN'])->count();
                $total = $cr->students->count();
                $pdbk = $cr->students->filter(function($s) {
                    return ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS') || str_contains(strtoupper($s->student_type), 'INKLUSI'))) || !empty($s->special_needs_type) || !empty($s->gpk_employee_id);
                })->count();

                $gpkTeachers = $cr->students->whereNotNull('gpkTeacher')->pluck('gpkTeacher.name')->unique()->values()->all();

                $classroomsData[] = [
                    'id' => $cr->id,
                    'name' => $cr->name,
                    'code' => $cr->code ?: '-',
                    'full_name' => $cr->full_name,
                    'capacity' => $cr->capacity ?: 30,
                    'homeroom_teacher' => $cr->homeroomTeacher?->name,
                    'homeroom_teacher_title' => $cr->homeroomTeacher?->position ?: 'Wali Kelas',
                    'gpk_teachers' => $gpkTeachers,
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

        $malePercent = $grandTotalStudents > 0 ? round(($grandTotalMale / $grandTotalStudents) * 100, 1) : 0;
        $femalePercent = $grandTotalStudents > 0 ? round(($grandTotalFemale / $grandTotalStudents) * 100, 1) : 0;
        $pdbkPercent = $grandTotalStudents > 0 ? round(($grandTotalPdbk / $grandTotalStudents) * 100, 1) : 0;

        return [
            'levelReports' => $levelReports,
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
}
