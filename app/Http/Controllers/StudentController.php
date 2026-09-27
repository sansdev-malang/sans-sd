<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StudentController extends Controller
{
    /**
     * Build the filtered query and resolve academic years for student listings and exports.
     */
    private function getFilteredStudentsQuery(Request $request): array
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Unique yearly academic years for annual entities (Tahunan - Opsi A)
        $uniqueAcademicYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Default to active academic year if not explicitly selected
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id');

        $query = Student::with([
            'classroom.classLevel', 
            'classroom.homeroomTeacher', 
            'academicYear', 
            'spmbCandidate', 
            'gpkTeacher'
        ]);

        // Academic Year Filter (covers all semester records of the selected annual year)
        if ($matchingYearIds->isNotEmpty()) {
            $query->whereIn('academic_year_id', $matchingYearIds);
        }

        // Search query
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('special_needs_type', 'like', "%{$search}%")
                  ->orWhereHas('gpkTeacher', function($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Kelas (ClassLevel)
        if ($classLevelId = $request->get('class_level_id')) {
            if ($classLevelId !== 'all') {
                $query->whereHas('classroom', function ($q) use ($classLevelId) {
                    $q->where('class_level_id', $classLevelId);
                });
            }
        }

        // Filter: Rombel (Classroom)
        if ($classroomId = $request->get('classroom_id')) {
            if ($classroomId !== 'all') {
                $query->where('classroom_id', $classroomId);
            }
        }

        // Filter: Tipe Siswa (Reguler / Inklusi PDBK)
        if ($studentType = $request->get('student_type')) {
            if ($studentType === 'PDBK') {
                $query->where(function($q) {
                    $q->where('student_type', 'like', '%PDBK%')
                      ->orWhere('student_type', 'like', '%KHUSUS%')
                      ->orWhere('student_type', 'like', '%INKLUSI%')
                      ->orWhereNotNull('gpk_employee_id')
                      ->orWhereNotNull('special_needs_type');
                });
            } elseif ($studentType === 'REGULER') {
                $query->where(function($q) {
                    $q->where('student_type', 'like', '%REGULER%')
                      ->orWhereNull('student_type');
                })->whereNull('special_needs_type')->whereNull('gpk_employee_id');
            }
        }

        // Filter: Status
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter: Gender
        if ($gender = $request->get('gender')) {
            if ($gender !== 'all') {
                $query->where('gender', $gender);
            }
        }

        return [
            'query' => $query,
            'academicYears' => $academicYears,
            'uniqueAcademicYears' => $uniqueAcademicYears,
            'selectedYear' => $selectedYear,
            'selectedYearId' => $selectedYearId,
            'selectedYearName' => $selectedYearName,
            'matchingYearIds' => $matchingYearIds,
            'activeAcademicYear' => $activeAcademicYear,
        ];
    }

    /**
     * Display a listing of students with filters & pagination.
     */
    public function index(Request $request)
    {
        $filterData = $this->getFilteredStudentsQuery($request);
        $query = $filterData['query'];
        $matchingYearIds = $filterData['matchingYearIds'];
        $uniqueAcademicYears = $filterData['uniqueAcademicYears'];
        $activeAcademicYear = $filterData['activeAcademicYear'];
        $selectedYearId = $filterData['selectedYearId'];
        $selectedYear = $filterData['selectedYear'];
        $selectedYearName = $filterData['selectedYearName'];

        // Stats calculation based on selected academic year
        $statsQuery = Student::query();
        if ($matchingYearIds->isNotEmpty()) {
            $statsQuery->whereIn('academic_year_id', $matchingYearIds);
        }

        $totalStudents = (clone $statsQuery)->count();
        $activeStudents = (clone $statsQuery)->where('status', 'aktif')->count();
        $maleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['L', 'Laki-laki', 'Male'])->count();
        $femaleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['P', 'Perempuan', 'Female'])->count();
        $pdbkStudents = (clone $statsQuery)->where('status', 'aktif')->where(function($q) {
            $q->where('student_type', 'like', '%PDBK%')
              ->orWhere('student_type', 'like', '%KHUSUS%')
              ->orWhere('student_type', 'like', '%INKLUSI%')
              ->orWhereNotNull('gpk_employee_id')
              ->orWhereNotNull('special_needs_type');
        })->count();
        
        $rombelQuery = Classroom::where('is_active', true);
        if ($matchingYearIds->isNotEmpty()) {
            $rombelQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $totalClassrooms = $rombelQuery->count();

        $stats = [
            'total_active' => $activeStudents,
            'total_all' => $totalStudents,
            'male' => $maleStudents,
            'female' => $femaleStudents,
            'pdbk' => $pdbkStudents,
            'classrooms' => $totalClassrooms,
        ];

        // Master lists for filter dropdowns & modal selects
        $classLevels = ClassLevel::orderBy('order')->get();
        
        $classroomListQuery = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true);
        if ($matchingYearIds->isNotEmpty()) {
            $classroomListQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $classrooms = $classroomListQuery->orderBy('class_level_id')->orderBy('code')->orderBy('name')->get();
        $allClassrooms = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true)->orderBy('academic_year_id', 'desc')->orderBy('name')->get();

        // Master daftar guru untuk pilihan Guru Pendamping Khusus (GPK / Shadow Teacher)
        $teachers = Employee::whereIn('status', ['Active', 'aktif', 'active', 'Aktif'])
            ->where(function($q) {
                $q->where('position', 'like', '%GPK%')
                  ->orWhere('position', 'like', '%Pendamping%')
                  ->orWhere('position', 'like', '%Shadow%')
                  ->orWhere('position', 'like', '%Inklusi%')
                  ->orWhereHas('employeeType', function($et) {
                      $et->where('name', 'like', '%GPK%')
                         ->orWhere('name', 'like', '%Pendamping%')
                         ->orWhere('name', 'like', '%Shadow%')
                         ->orWhere('name', 'like', '%Inklusi%');
                  });
            })
            ->orderBy('name')
            ->get();
        if ($teachers->isEmpty()) {
            $teachers = Employee::whereIn('status', ['Active', 'aktif', 'active', 'Aktif'])
                ->whereHas('employeeType', fn($et) => $et->where('name', 'like', '%Guru%'))
                ->orderBy('name')
                ->get();
        }

        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || (int)$perPage >= 999999) {
            $totalCount = (clone $query)->count();
            $students = $query->orderBy('status', 'asc')->orderBy('full_name', 'asc')->paginate(max($totalCount, 1))->withQueryString();
        } else {
            $perPageVal = in_array((int)$perPage, [10, 15, 25, 50, 100, 200]) ? (int)$perPage : 15;
            $students = $query->orderBy('status', 'asc')->orderBy('full_name', 'asc')->paginate($perPageVal)->withQueryString();
        }

        return view('admin.students.index', [
            'students' => $students,
            'stats' => $stats,
            'classLevels' => $classLevels,
            'classrooms' => $classrooms,
            'allClassrooms' => $allClassrooms,
            'teachers' => $teachers,
            'academicYears' => $uniqueAcademicYears,
            'activeAcademicYear' => $activeAcademicYear,
            'selectedYearId' => $selectedYearId,
            'selectedYear' => $selectedYear,
            'selectedYearName' => $selectedYearName,
        ]);
    }

    /**
     * Export filtered students to Excel (.xlsx) with professional Dapodik formatting.
     */
    public function exportExcel(Request $request)
    {
        $filterData = $this->getFilteredStudentsQuery($request);
        $students = $filterData['query']->orderBy('classroom_id', 'asc')->orderBy('full_name', 'asc')->get();
        $selectedYearName = $filterData['selectedYearName'] ?: 'Semua Tapel';
        $appName = function_exists('setting') ? setting('app_name', 'SD Anak Saleh') : 'SD Anak Saleh';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Document Titles
        $sheet->setCellValue('A1', "DATA PESERTA DIDIK " . strtoupper($appName));
        $sheet->setCellValue('A2', "Tahun Pelajaran: " . $selectedYearName . " | Dicetak: " . date('d/m/Y H:i') . " WIB");
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('1E1B4B');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('475569');

        // Table Headers
        $headers = [
            'No',
            'NIS',
            'NISN',
            'NIK',
            'No. Kartu Keluarga',
            'Nama Lengkap Siswa',
            'Nama Panggilan',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Agama',
            'Tingkat',
            'Rombel',
            'Tahun Pelajaran',
            'Kategori Siswa',
            'Jenis Kebutuhan Khusus',
            'Guru Pendamping Khusus (GPK)',
            'Wali Kelas',
            'Nama Ayah',
            'Pekerjaan Ayah',
            'No. HP/WA Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'No. HP/WA Ibu',
            'No. WhatsApp Utama',
            'Email Orang Tua',
            'Alamat Domisili',
            'RT',
            'RW',
            'Kelurahan / Desa',
            'Kecamatan',
            'Kota / Kabupaten',
            'Provinsi',
            'Status Siswa',
            'Tanggal Masuk / Diterima'
        ];

        $headerRow = 4;
        foreach ($headers as $colIdx => $title) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . $headerRow, $title);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));
        
        // Header Styling
        $sheet->getStyle("A{$headerRow}:{$lastColLetter}{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '312E81'], // Deep Indigo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '6366F1'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        $row = 5;
        $no = 1;
        $countMale = 0;
        $countFemale = 0;
        $countPdbk = 0;

        foreach ($students as $student) {
            $isFemale = in_array(strtoupper((string)$student->gender), ['P', 'PEREMPUAN', 'FEMALE']);
            $genderCode = $isFemale ? 'P' : 'L';
            if ($genderCode === 'L') $countMale++; else $countFemale++;

            $isPdbk = ($student->student_type && (str_contains(strtoupper($student->student_type), 'PDBK') || str_contains(strtoupper($student->student_type), 'KHUSUS') || str_contains(strtoupper($student->student_type), 'INKLUSI'))) || !empty($student->special_needs_type) || !empty($student->gpk_employee_id);
            if ($isPdbk) $countPdbk++;

            $kategoriText = $isPdbk ? 'PDBK' : 'REGULER';
            $statusText = strtoupper($student->status ?: 'AKTIF');

            $birthDateFormatted = $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-';
            $enrolledDateFormatted = $student->enrolled_date ? \Carbon\Carbon::parse($student->enrolled_date)->format('d/m/Y') : '-';

            // Explicit String values for numeric codes (preserves leading zeroes)
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValueExplicit('B' . $row, (string)($student->nis ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, (string)($student->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, (string)($student->nik ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, (string)($student->no_kk ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $row, strtoupper($student->full_name ?? '-'));
            $sheet->setCellValue('G' . $row, $student->nickname ?? '-');
            $sheet->setCellValue('H' . $row, $genderCode);
            $sheet->setCellValue('I' . $row, $student->birth_place ?? '-');
            $sheet->setCellValue('J' . $row, $birthDateFormatted);
            $sheet->setCellValue('K' . $row, $student->age ?? '-');
            $sheet->setCellValue('L' . $row, $student->religion ?? 'Islam');
            $sheet->setCellValue('M' . $row, $student->classroom?->classLevel?->name ?? '-');
            $sheet->setCellValue('N' . $row, $student->classroom?->full_name ?? ($student->classroom?->name ?? '-'));
            $sheet->setCellValue('O' . $row, $student->academicYear?->name ?? '-');
            $sheet->setCellValue('P' . $row, $kategoriText);
            $sheet->setCellValue('Q' . $row, $student->special_needs_type ?? '-');
            $sheet->setCellValue('R' . $row, $student->gpkTeacher?->name ?? '-');
            $sheet->setCellValue('S' . $row, $student->classroom?->homeroomTeacher?->name ?? '-');
            $sheet->setCellValue('T' . $row, $student->father_name ?? '-');
            $sheet->setCellValue('U' . $row, $student->father_job ?? '-');
            $sheet->setCellValueExplicit('V' . $row, (string)($student->father_phone ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('W' . $row, $student->mother_name ?? '-');
            $sheet->setCellValue('X' . $row, $student->mother_job ?? '-');
            $sheet->setCellValueExplicit('Y' . $row, (string)($student->mother_phone ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('Z' . $row, (string)($student->parent_phone ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('AA' . $row, $student->parent_email ?? '-');
            $sheet->setCellValue('AB' . $row, $student->address ?? '-');
            $sheet->setCellValue('AC' . $row, $student->rt ?? '-');
            $sheet->setCellValue('AD' . $row, $student->rw ?? '-');
            $sheet->setCellValue('AE' . $row, $student->village ?? '-');
            $sheet->setCellValue('AF' . $row, $student->district ?? '-');
            $sheet->setCellValue('AG' . $row, $student->city ?? '-');
            $sheet->setCellValue('AH' . $row, $student->province ?? '-');
            $sheet->setCellValue('AI' . $row, $statusText);
            $sheet->setCellValue('AJ' . $row, $enrolledDateFormatted);

            // Row Border & Zebra Striping
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            // Alignments for specific columns
            $sheet->getStyle("A{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$row}:M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("P{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("AI{$row}:AJ{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Summary Statistics Footer Row
        $summaryRow = $row;
        $sheet->setCellValue("A{$summaryRow}", "TOTAL PESERTA DIDIK TERDATA: " . count($students) . " Siswa (Putra: {$countMale}, Putri: {$countFemale}, PDBK/Inklusi: {$countPdbk})");
        $sheet->mergeCells("A{$summaryRow}:{$lastColLetter}{$summaryRow}");
        $sheet->getStyle("A{$summaryRow}:{$lastColLetter}{$summaryRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F172A']]],
        ]);
        $sheet->getRowDimension($summaryRow)->setRowHeight(24);

        // Auto-fit column widths
        foreach (range(1, count($headers)) as $colIdx) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $cleanYear = str_replace(['/', ' '], '_', $selectedYearName);
        $fileName = 'Data_Siswa_' . str_replace([' ', '.'], '_', $appName) . '_' . $cleanYear . '_' . date('Ymd_His') . '.xlsx';

        if ($request->filled('download_token')) {
            setcookie('download_token', $request->query('download_token'), time() + 60, '/', '', false, false);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Print-friendly view of filtered student data (with letterhead and print CSS).
     */
    public function print(Request $request)
    {
        $filterData = $this->getFilteredStudentsQuery($request);
        $students = $filterData['query']->orderBy('classroom_id', 'asc')->orderBy('full_name', 'asc')->get();

        $selectedClassLevel = null;
        if ($request->filled('class_level_id') && $request->get('class_level_id') !== 'all') {
            $selectedClassLevel = ClassLevel::find($request->get('class_level_id'));
        }

        $selectedClassroom = null;
        if ($request->filled('classroom_id') && $request->get('classroom_id') !== 'all') {
            $selectedClassroom = Classroom::with('classLevel')->find($request->get('classroom_id'));
        }

        return view('admin.students.print', [
            'students' => $students,
            'selectedYear' => $filterData['selectedYear'],
            'selectedYearName' => $filterData['selectedYearName'],
            'activeAcademicYear' => $filterData['activeAcademicYear'],
            'selectedClassLevel' => $selectedClassLevel,
            'selectedClassroom' => $selectedClassroom,
            'selectedStudentType' => $request->get('student_type'),
            'selectedStatus' => $request->get('status'),
            'searchQuery' => $request->get('search'),
        ]);
    }

    /**
     * Export students to PDF download.
     */
    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        $filterData = $this->getFilteredStudentsQuery($request);
        $students = $filterData['query']->orderBy('classroom_id', 'asc')->orderBy('full_name', 'asc')->get();
        $selectedYearName = $filterData['selectedYearName'] ?: 'Semua_Tapel';
        $appName = function_exists('setting') ? setting('app_name', 'SD Anak Saleh') : 'SD Anak Saleh';

        $selectedClassLevel = null;
        if ($request->filled('class_level_id') && $request->get('class_level_id') !== 'all') {
            $selectedClassLevel = ClassLevel::find($request->get('class_level_id'));
        }

        $selectedClassroom = null;
        if ($request->filled('classroom_id') && $request->get('classroom_id') !== 'all') {
            $selectedClassroom = Classroom::with('classLevel')->find($request->get('classroom_id'));
        }

        $data = [
            'students' => $students,
            'selectedYear' => $filterData['selectedYear'],
            'selectedYearName' => $selectedYearName,
            'activeAcademicYear' => $filterData['activeAcademicYear'],
            'selectedClassLevel' => $selectedClassLevel,
            'selectedClassroom' => $selectedClassroom,
            'selectedStudentType' => $request->get('student_type'),
            'selectedStatus' => $request->get('status'),
            'searchQuery' => $request->get('search'),
            'appName' => $appName,
        ];

        $cleanYear = str_replace(['/', ' '], '_', $selectedYearName);
        $fileName = 'Data_Siswa_' . str_replace([' ', '.'], '_', $appName) . '_' . $cleanYear . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.students.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true);

        return $pdf->download($fileName);
    }

    /**
     * Show single student detail (JSON) with full profile & lifecycle history.
     */
    public function show($id): JsonResponse
    {
        $student = Student::with([
            'classroom.classLevel', 
            'classroom.homeroomTeacher', 
            'academicYear', 
            'spmbCandidate',
            'gpkTeacher',
            'classroomHistories.classroom.classLevel',
            'classroomHistories.classroom.homeroomTeacher',
            'classroomHistories.academicYear'
        ])->findOrFail($id);

        // Hitung kelengkapan data (Completeness %)
        $requiredFields = [
            'nis', 'nik', 'full_name', 'gender', 'birth_place', 'birth_date', 
            'religion', 'address', 'father_name', 'mother_name', 'parent_phone',
            'no_kk', 'birth_certificate_no', 'blood_type'
        ];
        $filledCount = 0;
        foreach ($requiredFields as $field) {
            if (!empty($student->$field)) {
                $filledCount++;
            }
        }
        $completenessPercent = round(($filledCount / count($requiredFields)) * 100);

        return response()->json([
            'success' => true,
            'student' => $student->load(['gpkTeacher', 'classroom.classLevel', 'academicYear']),
            'completeness_percent' => $completenessPercent,
            'formatted_gender' => $student->formatted_gender,
            'age' => $student->age,
            'whatsapp_url' => $student->whatsapp_url,
            'clean_phone' => $student->clean_parent_phone,
            'classroom_histories' => $student->classroomHistories()->with(['academicYear', 'classroom'])->get(),
        ]);
    }

    /**
     * Store a newly created student in storage (All 7 categories).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // 1. Identitas & Legalitas
            'nis' => 'required|string|max:50|unique:students,nis',
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'birth_certificate_no' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            
            // 2. Inklusi & Kekhususan
            'student_type' => 'nullable|string|max:100',
            'special_needs_type' => 'nullable|string|max:255',
            'special_needs_notes' => 'nullable|string',
            'gpk_employee_id' => 'nullable|exists:employees,id',

            // 3. Alamat & Domisili
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:20',
            'rw' => 'nullable|string|max:20',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'district_category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'residence_status' => 'nullable|string|max:100',
            'distance_to_school' => 'nullable|string|max:50',
            'home_phone' => 'nullable|string|max:50',

            // 4. Keluarga & Saudara
            'child_number' => 'nullable|integer',
            'siblings_count' => 'nullable|integer',
            'step_siblings_count' => 'nullable|integer',
            'adoptive_siblings_count' => 'nullable|integer',
            'home_language' => 'nullable|string|max:100',

            // 5. Kesehatan & UKS
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:10',
            'severe_disease_history' => 'nullable|string',
            'frequent_disease' => 'nullable|string',

            // 6. Orang Tua & Wali
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|max:50',
            'father_birth_place' => 'nullable|string|max:100',
            'father_birth_date' => 'nullable|date',
            'father_religion' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|max:50',
            'father_education' => 'nullable|string|max:100',
            'father_job' => 'nullable|string|max:100',
            'father_company' => 'nullable|string|max:255',
            'father_income' => 'nullable|string|max:100',
            'father_email' => 'nullable|email|max:100',

            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|max:50',
            'mother_birth_place' => 'nullable|string|max:100',
            'mother_birth_date' => 'nullable|date',
            'mother_religion' => 'nullable|string|max:50',
            'mother_phone' => 'nullable|string|max:50',
            'mother_education' => 'nullable|string|max:100',
            'mother_job' => 'nullable|string|max:100',
            'mother_company' => 'nullable|string|max:255',
            'mother_income' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:100',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_job' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',

            'parent_phone' => 'nullable|string|max:50',
            'parent_email' => 'nullable|email|max:100',

            // 7. Riwayat Asal Sekolah & Dokumen
            'previous_school' => 'nullable|string|max:255',
            'origin_category' => 'nullable|string|max:100',
            'previous_school_address' => 'nullable|string',
            'sttb_number_date' => 'nullable|string|max:255',
            'checklist_documents' => 'nullable|array',

            // Penempatan Kelas & Status
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'enrolled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['academic_year_id'])) {
            $classroom = Classroom::find($validated['classroom_id']);
            $validated['academic_year_id'] = $classroom?->academic_year_id;
            
            if (empty($validated['academic_year_id'])) {
                $activeAY = AcademicYear::where('is_active', true)->first();
                $validated['academic_year_id'] = $activeAY ? $activeAY->id : null;
            }
        }

        if (array_key_exists('enrolled_date', $validated) && empty($validated['enrolled_date'])) {
            $validated['enrolled_date'] = null;
        }

        // WhatsApp / Parent Phone fallback
        if (empty($validated['parent_phone'])) {
            $validated['parent_phone'] = $validated['father_phone'] ?? ($validated['mother_phone'] ?? ($validated['guardian_phone'] ?? null));
        }

        // Reset GPK jika tipe siswa reguler
        if (isset($validated['student_type']) && strtoupper($validated['student_type']) === 'REGULER') {
            $validated['gpk_employee_id'] = null;
        }

        $student = Student::create($validated);

        // Catat riwayat kelas awal (Lifecycle History)
        if ($student->classroom_id && $student->academic_year_id) {
            $currentClassroom = Classroom::with(['classLevel', 'homeroomTeacher'])->find($student->classroom_id);
            $gpkTeacher = $student->gpkTeacher;

            \App\Models\StudentClassroomHistory::firstOrCreate([
                'student_id' => $student->id,
                'academic_year_id' => $student->academic_year_id,
            ], [
                'classroom_id' => $student->classroom_id,
                'grade_level' => $currentClassroom?->classLevel?->name ?? ($currentClassroom?->classLevel?->order ? 'Kelas ' . $currentClassroom->classLevel->order : substr($currentClassroom?->name ?? '', 0, 1)),
                'classroom_name' => $currentClassroom?->name,
                'homeroom_teacher_name' => $currentClassroom?->homeroomTeacher?->name,
                'gpk_teacher_name' => $gpkTeacher?->name,
                'status' => 'aktif',
                'start_date' => $student->enrolled_date ?: now()->toDateString(),
                'notes' => 'Pendaftaran / Penempatan Rombel Awal',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$student->full_name} berhasil ditambahkan.",
            'student' => $student->load('gpkTeacher'),
        ]);
    }

    /**
     * Update student details (All 7 categories).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            // 1. Identitas & Legalitas
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'birth_certificate_no' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            
            // 2. Inklusi & Kekhususan
            'student_type' => 'nullable|string|max:100',
            'special_needs_type' => 'nullable|string|max:255',
            'special_needs_notes' => 'nullable|string',
            'gpk_employee_id' => 'nullable|exists:employees,id',

            // 3. Alamat & Domisili
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:20',
            'rw' => 'nullable|string|max:20',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'district_category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'residence_status' => 'nullable|string|max:100',
            'distance_to_school' => 'nullable|string|max:50',
            'home_phone' => 'nullable|string|max:50',

            // 4. Keluarga & Saudara
            'child_number' => 'nullable|integer',
            'siblings_count' => 'nullable|integer',
            'step_siblings_count' => 'nullable|integer',
            'adoptive_siblings_count' => 'nullable|integer',
            'home_language' => 'nullable|string|max:100',

            // 5. Kesehatan & UKS
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:10',
            'severe_disease_history' => 'nullable|string',
            'frequent_disease' => 'nullable|string',

            // 6. Orang Tua & Wali
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|max:50',
            'father_birth_place' => 'nullable|string|max:100',
            'father_birth_date' => 'nullable|date',
            'father_religion' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|max:50',
            'father_education' => 'nullable|string|max:100',
            'father_job' => 'nullable|string|max:100',
            'father_company' => 'nullable|string|max:255',
            'father_income' => 'nullable|string|max:100',
            'father_email' => 'nullable|email|max:100',

            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|max:50',
            'mother_birth_place' => 'nullable|string|max:100',
            'mother_birth_date' => 'nullable|date',
            'mother_religion' => 'nullable|string|max:50',
            'mother_phone' => 'nullable|string|max:50',
            'mother_education' => 'nullable|string|max:100',
            'mother_job' => 'nullable|string|max:100',
            'mother_company' => 'nullable|string|max:255',
            'mother_income' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:100',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_job' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',

            'parent_phone' => 'nullable|string|max:50',
            'parent_email' => 'nullable|email|max:100',

            // 7. Riwayat Asal Sekolah & Dokumen
            'previous_school' => 'nullable|string|max:255',
            'origin_category' => 'nullable|string|max:100',
            'previous_school_address' => 'nullable|string',
            'sttb_number_date' => 'nullable|string|max:255',
            'checklist_documents' => 'nullable|array',

            // Penempatan Kelas & Status
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'enrolled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (array_key_exists('enrolled_date', $validated) && empty($validated['enrolled_date'])) {
            $validated['enrolled_date'] = null;
        }

        // WhatsApp / Parent Phone fallback
        if (empty($validated['parent_phone'])) {
            $validated['parent_phone'] = $validated['father_phone'] ?? ($validated['mother_phone'] ?? ($validated['guardian_phone'] ?? null));
        }

        // Reset GPK jika tipe siswa reguler
        if (isset($validated['student_type']) && strtoupper($validated['student_type']) === 'REGULER') {
            $validated['gpk_employee_id'] = null;
        }

        $student->update($validated);

        // Update / create history record for current academic year & classroom with snapshots
        if ($student->classroom_id && $student->academic_year_id) {
            $currentClassroom = Classroom::with(['classLevel', 'homeroomTeacher'])->find($student->classroom_id);
            $gpkTeacher = $student->gpkTeacher;

            \App\Models\StudentClassroomHistory::updateOrCreate([
                'student_id' => $student->id,
                'academic_year_id' => $student->academic_year_id,
            ], [
                'classroom_id' => $student->classroom_id,
                'grade_level' => $currentClassroom?->classLevel?->name ?? ($currentClassroom?->classLevel?->order ? 'Kelas ' . $currentClassroom->classLevel->order : substr($currentClassroom?->name ?? '', 0, 1)),
                'classroom_name' => $currentClassroom?->name,
                'homeroom_teacher_name' => $currentClassroom?->homeroomTeacher?->name,
                'gpk_teacher_name' => $gpkTeacher?->name,
                'status' => $student->status === 'lulus' ? 'lulus' : ($student->status === 'mutasi' ? 'mutasi_keluar' : 'aktif'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$student->full_name} berhasil diperbarui.",
            'student' => $student,
        ]);
    }

    /**
     * Delete student.
     */
    public function destroy($id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $name = $student->full_name;

        // If linked to SPMB candidate, unlink it
        if ($student->spmb_candidate_id) {
            $candidate = \App\Models\SpmbCandidate::find($student->spmb_candidate_id);
            if ($candidate) {
                $candidate->is_enrolled = false;
                $candidate->enrolled_at = null;
                $candidate->student_id = null;
                $candidate->save();
            }
        }

        // Clean any linked classroom history
        \App\Models\StudentClassroomHistory::where('student_id', $student->id)->delete();

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$name} berhasil dihapus.",
        ]);
    }

    /**
     * Download Excel template for Bulk Student Import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // ==========================================
        // Sheet 1: Template Import Siswa
        // ==========================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Comprehensive Headers (All 7 Categories + Rombel/Tapel)
        $headers = [
            // 1. Identitas & Legalitas
            'NIS (Wajib)',
            'Nama Lengkap (Wajib)',
            'Nama Panggilan',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'NISN',
            'NIK Siswa',
            'No KK',
            'No Akta Kelahiran',
            'Agama',
            'Kewarganegaraan',

            // 2. Inklusi & Kebutuhan Khusus
            'Tipe Siswa (Reguler/PDBK)',
            'Jenis Kebutuhan Khusus',
            'Catatan Kebutuhan Khusus',

            // 3. Penempatan Rombel & Tapel
            'Kelas / Grade (e.g. 1A)',
            'Nama Kelas (e.g. Berlian)',
            'Tahun Pelajaran (e.g. 2026/2027)',
            'Tanggal Diterima (YYYY-MM-DD)',
            'Status Siswa (aktif/lulus/mutasi/keluar)',

            // 4. Alamat & Domisili
            'Alamat Rumah',
            'RT',
            'RW',
            'Kelurahan / Desa',
            'Kecamatan',
            'Kategori Wilayah (Dalam Kota/Luar Kota)',
            'Kota / Kabupaten',
            'Provinsi',
            'Kode Pos',
            'Status Tempat Tinggal',
            'Jarak ke Sekolah',
            'Telepon Rumah',

            // 5. Kontak Utama
            'No WhatsApp Utama Ortu',
            'Email Utama Ortu',

            // 6. Keluarga & Saudara
            'Anak Ke',
            'Jumlah Saudara Kandung',
            'Jumlah Saudara Tiri',
            'Jumlah Saudara Angkat',
            'Bahasa Sehari-hari',

            // 7. Kesehatan & Fisik (UKS)
            'Golongan Darah (A/B/AB/O)',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Riwayat Penyakit Berat',
            'Penyakit Sering Diderita',

            // 8. Data Ayah
            'Nama Ayah',
            'NIK Ayah',
            'Tempat Lahir Ayah',
            'Tanggal Lahir Ayah (YYYY-MM-DD)',
            'Agama Ayah',
            'No HP Ayah',
            'Pendidikan Ayah',
            'Pekerjaan Ayah',
            'Instansi / Kantor Ayah',
            'Alamat Kantor Ayah',
            'Telp Kantor Ayah',
            'Penghasilan Ayah',
            'Email Ayah',

            // 9. Data Ibu
            'Nama Ibu',
            'NIK Ibu',
            'Tempat Lahir Ibu',
            'Tanggal Lahir Ibu (YYYY-MM-DD)',
            'Agama Ibu',
            'No HP Ibu',
            'Pendidikan Ibu',
            'Pekerjaan Ibu',
            'Instansi / Kantor Ibu',
            'Alamat Kantor Ibu',
            'Telp Kantor Ibu',
            'Penghasilan Ibu',
            'Email Ibu',

            // 10. Data Wali
            'Nama Wali',
            'Hubungan Wali',
            'Tempat Lahir Wali',
            'Tanggal Lahir Wali (YYYY-MM-DD)',
            'Agama Wali',
            'No HP Wali',
            'Pendidikan Wali',
            'Pekerjaan Wali',
            'Alamat Wali',

            // 11. Riwayat Asal Sekolah & Catatan
            'Kategori Asal (TK/PAUD/Pindahan)',
            'Nama Asal Sekolah',
            'Alamat Asal Sekolah',
            'Nomor & Tanggal STTB',
            'Catatan Tambahan',
        ];

        // Sample Data Row (Carefully formatted strings to demonstrate correct data formats)
        $example = [
            // Identitas
            '26.SD.001',
            'Muhammad Fauzi Pratama',
            'Fauzi',
            'L',
            'Malang',
            '2019-05-12',
            '0123456789',
            '3573010101190001',
            '3573010101180001',
            '12345/DIS/2019',
            'Islam',
            'WNI',

            // Inklusi
            'reguler',
            '',
            '',

            // Rombel & Tapel
            '1A',
            'Berlian',
            '2026/2027',
            '2026-07-15',
            'aktif',

            // Alamat
            'Jl. Soekarno Hatta No. 45, RT 02 RW 05',
            '02',
            '05',
            'Mojolangu',
            'Lowokwaru',
            'Dalam Kota',
            'Kota Malang',
            'Jawa Timur',
            '65142',
            'Bersama Orang Tua',
            '2.5 km',
            '0341-412345',

            // Kontak Utama
            '081234567890',
            'ortu.fauzi@gmail.com',

            // Keluarga
            '1',
            '1',
            '0',
            '0',
            'Bahasa Indonesia',

            // UKS
            'O',
            '118',
            '21',
            'Tidak ada',
            'Flu / Batuk ringan',

            // Ayah
            'Budi Santoso',
            '3573010101780001',
            'Malang',
            '1978-03-20',
            'Islam',
            '081234567890',
            'S1 Teknik',
            'Karyawan Swasta',
            'PT. Telkom Indonesia',
            'Jl. Kayutangan No. 10 Malang',
            '0341-362222',
            'Rp 7.000.000 - Rp 10.000.000',
            'budi.santoso@gmail.com',

            // Ibu
            'Siti Aminah',
            '3573010101820002',
            'Surabaya',
            '1982-08-14',
            'Islam',
            '081234567891',
            'S1 Pendidikan',
            'Guru',
            'SD Negeri 1 Malang',
            'Jl. Bandung No. 5 Malang',
            '0341-551234',
            'Rp 3.000.000 - Rp 5.000.000',
            'siti.aminah@gmail.com',

            // Wali
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',

            // Asal Sekolah
            'TK',
            'TK Anak Saleh',
            'Jl. Candi Panggung No. 20, Malang',
            '012/TK-AS/2026',
            'Siswa pindahan atau peserta baru',
        ];

        // Put headers in row 1
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Put example in row 2 using TYPE_STRING (preserves leading zeros & prevents scientific notation)
        foreach ($example as $colIndex => $val) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValueExplicit($colLetter . '2', (string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        // Header styling
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F46E5'); // Premium Indigo

        // Auto-size all columns
        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ==========================================
        // Sheet 2: Referensi Rombel & Tapel
        // ==========================================
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Rombel & Tapel');

        $refHeaders = ['No', 'Tingkat', 'Kelas / Grade', 'Nama Kelas (Julukan)', 'Nama Rombel Resmi', 'Tahun Pelajaran', 'Wali Kelas'];
        foreach ($refHeaders as $idx => $rh) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $refSheet->setCellValue($colLetter . '1', $rh);
        }
        $refSheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $refSheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF10B981'); // Emerald

        $classrooms = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('class_level_id')
            ->orderBy('code')
            ->get();

        $rowIdx = 2;
        foreach ($classrooms as $no => $cr) {
            $refSheet->setCellValue('A' . $rowIdx, $no + 1);
            $refSheet->setCellValue('B' . $rowIdx, $cr->classLevel?->name ?: '-');
            $refSheet->setCellValue('C' . $rowIdx, $cr->code ?: '-');
            $refSheet->setCellValue('D' . $rowIdx, $cr->name ?: '-');
            $refSheet->setCellValue('E' . $rowIdx, $cr->full_name ?: '-');
            $refSheet->setCellValue('F' . $rowIdx, $cr->academicYear?->name ?: '-');
            $refSheet->setCellValue('G' . $rowIdx, $cr->homeroomTeacher?->name ?: '-');
            $rowIdx++;
        }

        foreach (range(1, 7) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $refSheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ==========================================
        // Sheet 3: Panduan Singkat
        // ==========================================
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle('Panduan Pengisian');
        $guideHeaders = ['Kolom / Data', 'Ketentuan Format & Nilai Valid', 'Contoh Pengisian'];
        foreach ($guideHeaders as $idx => $gh) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $guideSheet->setCellValue($colLetter . '1', $gh);
        }
        $guideSheet->getStyle('A1:C1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $guideSheet->getStyle('A1:C1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF0284C7'); // Sky Blue

        $guideData = [
            ['NIS', 'Nomor Induk Siswa (Wajib & Unik di sistem)', '26.SD.001'],
            ['Nama Lengkap', 'Nama lengkap siswa sesuai akta kelahiran (Wajib)', 'Muhammad Fauzi Pratama'],
            ['Jenis Kelamin', 'Ketik "L" untuk Laki-laki atau "P" untuk Perempuan', 'L atau P'],
            ['Tanggal Lahir / Diterima', 'Format tanggal YYYY-MM-DD atau DD/MM/YYYY atau teks nama bulan', '2019-05-12 atau 12/05/2019'],
            ['Kelas / Grade & Nama Kelas', 'Sesuaikan dengan data di sheet "Referensi Rombel & Tapel"', 'Grade: 1A, Nama Kelas: Berlian'],
            ['Tahun Pelajaran', 'Format tahun ajaran sekolah (misal: 2026/2027)', '2026/2027'],
            ['Status Siswa', 'Pilihan: aktif, lulus, mutasi, keluar, nonaktif (default: aktif)', 'aktif'],
            ['Tipe Siswa', 'Pilihan: reguler, pdbk, atau inklusi', 'reguler atau pdbk'],
            ['Nomor Identitas (NIK/KK/NISN)', 'Disimpan sebagai teks sehingga angka 0 di depan tidak akan hilang', '3573010101190001'],
            ['No Telepon / WhatsApp', 'Nomor HP aktif untuk broadcast WhatsApp pengumuman sekolah', '081234567890'],
        ];

        foreach ($guideData as $gRow => $gVals) {
            $rNum = $gRow + 2;
            $guideSheet->setCellValue('A' . $rNum, $gVals[0]);
            $guideSheet->setCellValue('B' . $rNum, $gVals[1]);
            $guideSheet->setCellValue('C' . $rNum, $gVals[2]);
        }

        foreach (range(1, 3) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $guideSheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        if (request()->filled('download_token')) {
            setcookie('download_token', request()->query('download_token'), time() + 60, '/', '', false, false);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Template_Import_Siswa_SD_Lengkap.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import students from Excel (Supports Official Template & Real TU Master Spreadsheets).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'default_classroom_id' => 'nullable|exists:classrooms,id',
            'default_academic_year_id' => 'nullable|exists:academic_years,id',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Optimized Reader for Speed and Large Datasets
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        // Select MASTER sheet if exists (like in TU files), otherwise use first sheet
        $sheet = $spreadsheet->getSheetByName('MASTER') ?: ($spreadsheet->getSheetByName('Data Siswa') ?: $spreadsheet->getActiveSheet());
        $rows = $sheet->toArray();

        if (empty($rows)) {
            return redirect()->route('students.index')->with('error', 'File Excel kosong atau tidak memiliki data.');
        }

        // Extract header row
        $header = array_shift($rows);

        $defaultClassroom = $request->filled('default_classroom_id') ? Classroom::find($request->input('default_classroom_id')) : null;
        $defaultAcademicYear = $request->filled('default_academic_year_id') ? AcademicYear::find($request->input('default_academic_year_id')) : AcademicYear::where('is_active', true)->first();

        // Build Flexible & Robust Column Map from Header Names
        $map = $this->buildImportColumnMap($header);

        $errors = [];
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Skip completely empty row
            if (empty(array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== ''))) {
                continue;
            }

            // Extract Identitas Wajib
            $nis = isset($map['nis']) && !empty($row[$map['nis']]) ? trim((string)$row[$map['nis']]) : null;
            $fullName = isset($map['full_name']) && !empty($row[$map['full_name']]) ? trim((string)$row[$map['full_name']]) : null;

            if (empty($nis)) {
                $errors[] = "Baris {$rowNumber}: NIS wajib diisi.";
                continue;
            }

            if (empty($fullName)) {
                $errors[] = "Baris {$rowNumber}: Nama lengkap wajib diisi.";
                continue;
            }

            // Helper to get string value
            $getVal = function ($key) use ($map, $row) {
                if (!isset($map[$key])) return null;
                $idx = $map[$key];
                if (!isset($row[$idx]) || is_null($row[$idx])) return null;
                $val = trim((string)$row[$idx]);
                return $val !== '' ? $val : null;
            };

            // Helper to get integer value
            $getInt = function ($key) use ($getVal) {
                $val = $getVal($key);
                return is_numeric($val) ? (int)$val : null;
            };

            // 1. Identitas & Legalitas
            $nickname = $getVal('nickname');
            $genderRaw = $getVal('gender');
            $birthPlace = $getVal('birth_place');
            $birthDateRaw = $getVal('birth_date');
            $nisn = $getVal('nisn');
            $nik = $getVal('nik');
            $noKk = $getVal('no_kk');
            $birthCertNo = $getVal('birth_certificate_no');
            $religion = $getVal('religion') ?: 'Islam';
            $citizenship = $getVal('citizenship') ?: 'WNI';

            // 2. Inklusi / PDBK
            $studentType = $getVal('student_type') ?: 'reguler';
            if (str_contains(strtolower($studentType), 'pdbk') || str_contains(strtolower($studentType), 'inklusi')) {
                $studentType = 'pdbk';
            } else {
                $studentType = 'reguler';
            }
            $specialNeedsType = $getVal('special_needs_type');
            $specialNeedsNotes = $getVal('special_needs_notes');

            // 3. Rombel & Tapel Resolusi
            $gradeCode = $getVal('code');
            $className = $getVal('class_name');
            $combinedRombel = $getVal('rombel_combined');
            $academicYearStr = $getVal('academic_year');
            $enrolledDateRaw = $getVal('enrolled_date');
            $statusRaw = $getVal('status');

            // 4. Alamat & Domisili
            $address = $getVal('address');
            $rt = $getVal('rt');
            $rw = $getVal('rw');
            $village = $getVal('village');
            $district = $getVal('district');
            $districtCategory = $getVal('district_category');
            $city = $getVal('city');
            $province = $getVal('province');
            $postalCode = $getVal('postal_code');
            $residenceStatus = $getVal('residence_status');
            $distanceToSchool = $getVal('distance_to_school');
            $homePhone = $getVal('home_phone');

            // 5. Kontak Utama
            $parentPhone = $getVal('parent_phone');
            $parentEmail = $getVal('parent_email');

            // 6. Keluarga & Saudara
            $childNumber = $getInt('child_number');
            $siblingsCount = $getInt('siblings_count');
            $stepSiblingsCount = $getInt('step_siblings_count');
            $adoptiveSiblingsCount = $getInt('adoptive_siblings_count');
            $homeLanguage = $getVal('home_language');

            // 7. Kesehatan & UKS
            $bloodType = $getVal('blood_type');
            $height = $getVal('height');
            $weight = $getVal('weight');
            $severeDisease = $getVal('severe_disease_history');
            $frequentDisease = $getVal('frequent_disease');

            // 8. Data Ayah
            $fatherName = $getVal('father_name');
            $fatherNik = $getVal('father_nik');
            $fatherBirthPlace = $getVal('father_birth_place');
            $fatherBirthDateRaw = $getVal('father_birth_date');
            $fatherReligion = $getVal('father_religion');
            $fatherPhone = $getVal('father_phone');
            $fatherEducation = $getVal('father_education');
            $fatherJob = $getVal('father_job');
            $fatherCompany = $getVal('father_company');
            $fatherCompanyAddress = $getVal('father_company_address');
            $fatherCompanyPhone = $getVal('father_company_phone');
            $fatherIncome = $getVal('father_income');
            $fatherEmail = $getVal('father_email');

            // 9. Data Ibu
            $motherName = $getVal('mother_name');
            $motherNik = $getVal('mother_nik');
            $motherBirthPlace = $getVal('mother_birth_place');
            $motherBirthDateRaw = $getVal('mother_birth_date');
            $motherReligion = $getVal('mother_religion');
            $motherPhone = $getVal('mother_phone');
            $motherEducation = $getVal('mother_education');
            $motherJob = $getVal('mother_job');
            $motherCompany = $getVal('mother_company');
            $motherCompanyAddress = $getVal('mother_company_address');
            $motherCompanyPhone = $getVal('mother_company_phone');
            $motherIncome = $getVal('mother_income');
            $motherEmail = $getVal('mother_email');

            // 10. Data Wali
            $guardianName = $getVal('guardian_name');
            $guardianRelation = $getVal('guardian_relation');
            $guardianBirthPlace = $getVal('guardian_birth_place');
            $guardianBirthDateRaw = $getVal('guardian_birth_date');
            $guardianReligion = $getVal('guardian_religion');
            $guardianPhone = $getVal('guardian_phone');
            $guardianEducation = $getVal('guardian_education');
            $guardianJob = $getVal('guardian_job');
            $guardianAddress = $getVal('guardian_address');

            // 11. Riwayat Asal Sekolah & Catatan
            $originCategory = $getVal('origin_category');
            $previousSchool = $getVal('previous_school');
            $previousSchoolAddress = $getVal('previous_school_address');
            $sttbNumberDate = $getVal('sttb_number_date');
            $notes = $getVal('notes');

            // Normalisasi Gender
            $gender = 'L';
            if ($genderRaw) {
                $gUpper = strtoupper($genderRaw);
                if (in_array($gUpper, ['L', 'LAKI-LAKI', 'MALE', 'PRIA', 'LK', 'M'])) {
                    $gender = 'L';
                } elseif (in_array($gUpper, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA', 'PR', 'F'])) {
                    $gender = 'P';
                }
            }

            // Normalisasi Tanggal
            $birthDate = $this->parseExcelDate($birthDateRaw);
            $fatherBirthDate = $this->parseExcelDate($fatherBirthDateRaw);
            $motherBirthDate = $this->parseExcelDate($motherBirthDateRaw);
            $guardianBirthDate = $this->parseExcelDate($guardianBirthDateRaw);
            $enrolledDate = $this->parseExcelDate($enrolledDateRaw);

            // Status Siswa
            $status = 'aktif';
            if ($statusRaw) {
                $sClean = strtolower(trim($statusRaw));
                if (in_array($sClean, ['aktif', 'lulus', 'mutasi', 'keluar', 'nonaktif'])) {
                    $status = $sClean;
                }
            }

            // Normalisasi Kontak Utama Fallback
            if (empty($parentPhone)) {
                $parentPhone = $fatherPhone ?: ($motherPhone ?: ($guardianPhone ?: null));
            }

            // Resolusi Tahun Ajaran
            $academicYearId = null;
            if ($academicYearStr) {
                $cleanAY = str_replace('-', '/', $academicYearStr);
                $ay = AcademicYear::where('name', $cleanAY)->orWhere('code', $cleanAY)->first();
                if ($ay) {
                    $academicYearId = $ay->id;
                }
            }
            if (!$academicYearId) {
                $academicYearId = $defaultAcademicYear?->id;
            }

            // Dapatkan semua ID semester dalam tahun ajaran yang sama
            $targetAY = $academicYearId ? AcademicYear::find($academicYearId) : null;
            $targetYearIds = $targetAY ? AcademicYear::where('name', $targetAY->name)->pluck('id')->toArray() : ($academicYearId ? [$academicYearId] : []);

            // Resolusi Rombel (Classroom) - Case-Insensitive
            $classroomId = null;

            if ($gradeCode && $className) {
                $cr = Classroom::whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                    ->whereRaw('LOWER(name) = ?', [strtolower($className)])
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if (!$cr) {
                    $cr = Classroom::whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                        ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                            $q->whereIn('academic_year_id', $targetYearIds);
                        })
                        ->first();
                }
                if (!$cr) {
                    $cr = Classroom::whereRaw('LOWER(name) = ?', [strtolower($className)])
                        ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                            $q->whereIn('academic_year_id', $targetYearIds);
                        })
                        ->first();
                }
                if ($cr) $classroomId = $cr->id;
            } elseif ($gradeCode) {
                $cr = Classroom::where(function($q) use ($gradeCode) {
                        $q->whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($gradeCode)]);
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            } elseif ($className) {
                $cr = Classroom::where(function($q) use ($className) {
                        $q->whereRaw('LOWER(name) = ?', [strtolower($className)])
                          ->orWhere('name', 'like', "%{$className}%");
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            } elseif ($combinedRombel) {
                $cleanCombined = trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $combinedRombel));
                $parts = preg_split('/\s+/', $cleanCombined);
                $firstPart = strtolower($parts[0] ?? '');
                $secondPart = strtolower($parts[1] ?? '');

                $cr = Classroom::where(function($q) use ($combinedRombel, $firstPart, $secondPart) {
                        $q->whereRaw('LOWER(name) = ?', [strtolower($combinedRombel)])
                          ->orWhereRaw('LOWER(code) = ?', [strtolower($combinedRombel)])
                          ->orWhereRaw('LOWER(code) = ?', [$firstPart])
                          ->orWhereRaw('LOWER(name) = ?', [$secondPart])
                          ->orWhere('name', 'like', "%{$combinedRombel}%");
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            }

            // Fallback ke Classroom Default jika dipilih di modal
            if (!$classroomId && $defaultClassroom) {
                $classroomId = $defaultClassroom->id;
                if (!$academicYearId && $defaultClassroom->academic_year_id) {
                    $academicYearId = $defaultClassroom->academic_year_id;
                }
            }

            if (!$classroomId) {
                $identifier = $gradeCode ? ($gradeCode . ' ' . $className) : ($className ?: ($combinedRombel ?: 'tidak terdefinisi'));
                $errors[] = "Baris {$rowNumber}: Rombel '{$identifier}' tidak ditemukan di sistem. Harap periksa nama rombel atau pilih Rombel default pada form import.";
                continue;
            }

            // Prepare Data Array for Upsert
            $dataToSave = [
                // Identitas & Legalitas
                'nis' => $nis,
                'nisn' => $nisn,
                'nik' => $nik,
                'no_kk' => $noKk,
                'birth_certificate_no' => $birthCertNo,
                'citizenship' => $citizenship,
                'full_name' => $fullName,
                'nickname' => $nickname,
                'gender' => $gender,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'religion' => $religion,

                // Inklusi
                'student_type' => $studentType,
                'special_needs_type' => $specialNeedsType,
                'special_needs_notes' => $specialNeedsNotes,

                // Rombel & Tapel
                'classroom_id' => $classroomId,
                'academic_year_id' => $academicYearId,
                'status' => $status,
                'enrolled_date' => $enrolledDate,

                // Alamat & Domisili
                'address' => $address,
                'rt' => $rt,
                'rw' => $rw,
                'village' => $village,
                'district' => $district,
                'district_category' => $districtCategory,
                'city' => $city,
                'province' => $province,
                'postal_code' => $postalCode,
                'residence_status' => $residenceStatus,
                'distance_to_school' => $distanceToSchool,
                'home_phone' => $homePhone,

                // Kontak Utama
                'parent_phone' => $parentPhone,
                'parent_email' => $parentEmail,

                // Keluarga & Saudara
                'child_number' => $childNumber,
                'siblings_count' => $siblingsCount,
                'step_siblings_count' => $stepSiblingsCount,
                'adoptive_siblings_count' => $adoptiveSiblingsCount,
                'home_language' => $homeLanguage,

                // Kesehatan & UKS
                'blood_type' => $bloodType,
                'height' => $height,
                'weight' => $weight,
                'severe_disease_history' => $severeDisease,
                'frequent_disease' => $frequentDisease,

                // Data Ayah
                'father_name' => $fatherName,
                'father_nik' => $fatherNik,
                'father_birth_place' => $fatherBirthPlace,
                'father_birth_date' => $fatherBirthDate,
                'father_religion' => $fatherReligion,
                'father_phone' => $fatherPhone,
                'father_education' => $fatherEducation,
                'father_job' => $fatherJob,
                'father_company' => $fatherCompany,
                'father_company_address' => $fatherCompanyAddress,
                'father_company_phone' => $fatherCompanyPhone,
                'father_income' => $fatherIncome,
                'father_email' => $fatherEmail,

                // Data Ibu
                'mother_name' => $motherName,
                'mother_nik' => $motherNik,
                'mother_birth_place' => $motherBirthPlace,
                'mother_birth_date' => $motherBirthDate,
                'mother_religion' => $motherReligion,
                'mother_phone' => $motherPhone,
                'mother_education' => $motherEducation,
                'mother_job' => $motherJob,
                'mother_company' => $motherCompany,
                'mother_company_address' => $motherCompanyAddress,
                'mother_company_phone' => $motherCompanyPhone,
                'mother_income' => $motherIncome,
                'mother_email' => $motherEmail,

                // Data Wali
                'guardian_name' => $guardianName,
                'guardian_relation' => $guardianRelation,
                'guardian_birth_place' => $guardianBirthPlace,
                'guardian_birth_date' => $guardianBirthDate,
                'guardian_religion' => $guardianReligion,
                'guardian_phone' => $guardianPhone,
                'guardian_education' => $guardianEducation,
                'guardian_job' => $guardianJob,
                'guardian_address' => $guardianAddress,

                // Asal Sekolah & Catatan
                'origin_category' => $originCategory,
                'previous_school' => $previousSchool,
                'previous_school_address' => $previousSchoolAddress,
                'sttb_number_date' => $sttbNumberDate,
                'notes' => $notes,
            ];

            // Filter null values if updating to avoid overwriting existing data with blanks
            $student = Student::where('nis', $nis)->first();

            if ($student) {
                $filteredData = array_filter($dataToSave, fn($v) => !is_null($v));
                $student->update($filteredData);
                $updatedCount++;
            } else {
                $student = Student::create($dataToSave);
                $importedCount++;
            }

            // Sync Classroom History
            if ($student && $student->classroom_id && $student->academic_year_id) {
                \App\Models\StudentClassroomHistory::updateOrCreate([
                    'student_id' => $student->id,
                    'academic_year_id' => $student->academic_year_id,
                ], [
                    'classroom_id' => $student->classroom_id,
                    'status' => $student->status === 'lulus' ? 'lulus' : ($student->status === 'mutasi' ? 'mutasi' : 'naik_kelas'),
                ]);
            }
        }

        $msg = "Proses impor selesai! {$importedCount} data siswa baru berhasil ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data siswa diperbarui";
        }
        $msg .= ".";

        if (count($errors) > 0) {
            return redirect()->route('students.index')
                ->with('success', $msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('students.index')->with('success', $msg);
    }

    /**
     * Parse flexible Excel date formats (Numeric serial, DD/MM/YYYY, YYYY-MM-DD, Indonesian month names).
     */
    protected function parseExcelDate($raw): ?string
    {
        if (empty($raw)) return null;

        try {
            $raw = trim((string)$raw);

            // 1. Excel numeric serial date (e.g. 43637 / 44002)
            if (is_numeric($raw) && (int)$raw > 10000 && (int)$raw < 70000) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$raw)->format('Y-m-d');
            }

            // 2. Format dd/mm/yyyy or dd-mm-yyyy or dd.mm.yyyy (e.g. 20/06/2019)
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $raw, $m)) {
                $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                $year = $m[3];
                if ((int)$day <= 31 && (int)$month <= 12) {
                    return "{$year}-{$month}-{$day}";
                }
                return \Carbon\Carbon::parse($raw)->format('Y-m-d');
            }

            // 3. Format yyyy-mm-dd or yyyy/mm/dd (e.g. 2019-06-20)
            if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $raw, $m)) {
                $year = $m[1];
                $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                $day = str_pad($m[3], 2, '0', STR_PAD_LEFT);
                return "{$year}-{$month}-{$day}";
            }

            // 4. Fallback Indonesian Month text (e.g. 20 Juni 2019)
            $indoMonths = [
                'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
                'april' => 'april', 'mei' => 'may', 'juni' => 'june',
                'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
                'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
                'agu' => 'aug', 'okt' => 'oct', 'des' => 'dec'
            ];
            $cleanDate = strtolower($raw);
            foreach ($indoMonths as $idm => $enm) {
                $cleanDate = str_replace($idm, $enm, $cleanDate);
            }
            return \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map Excel column headers flexibly to database attributes.
     */
    protected function buildImportColumnMap(array $header): array
    {
        $map = [];
        $hasSeparateRombel = false;

        foreach ($header as $cIdx => $cVal) {
            $rawText = trim((string)$cVal);
            $clean = strtolower($rawText);
            if ($clean === '') continue;

            // --- 1. DATA AYAH ---
            if (str_contains($clean, 'ayah')) {
                if (str_contains($clean, 'nik') || str_contains($clean, 'ktp')) {
                    $map['father_nik'] = $cIdx;
                } elseif (str_contains($clean, 'tempat lahir') || (str_contains($clean, 'tempat') && !str_contains($clean, 'bekerja'))) {
                    $map['father_birth_place'] = $cIdx;
                } elseif (str_contains($clean, 'tanggal') || str_contains($clean, 'tgl')) {
                    $map['father_birth_date'] = $cIdx;
                } elseif (str_contains($clean, 'agama')) {
                    $map['father_religion'] = $cIdx;
                } elseif (str_contains($clean, 'alamat') && (str_contains($clean, 'instansi') || str_contains($clean, 'kantor'))) {
                    $map['father_company_address'] = $cIdx;
                } elseif ((str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'hp')) && (str_contains($clean, 'instansi') || str_contains($clean, 'kantor'))) {
                    $map['father_company_phone'] = $cIdx;
                } elseif (str_contains($clean, 'instansi') || str_contains($clean, 'tempat bekerja') || str_contains($clean, 'perusahaan')) {
                    $map['father_company'] = $cIdx;
                } elseif (str_contains($clean, 'handphone') || str_contains($clean, 'hp') || str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'wa') || str_contains($clean, 'ponsel')) {
                    $map['father_phone'] = $cIdx;
                } elseif (str_contains($clean, 'pendidikan') || str_contains($clean, 'lulusan')) {
                    $map['father_education'] = $cIdx;
                } elseif (str_contains($clean, 'penghasilan') || str_contains($clean, 'gaji') || str_contains($clean, 'pendapatan')) {
                    $map['father_income'] = $cIdx;
                } elseif (str_contains($clean, 'email')) {
                    $map['father_email'] = $cIdx;
                } elseif (str_contains($clean, 'pekerjaan') || str_contains($clean, 'profesi') || str_contains($clean, 'kerja')) {
                    $map['father_job'] = $cIdx;
                } elseif (str_contains($clean, 'nama')) {
                    $map['father_name'] = $cIdx;
                }
                continue;
            }

            // --- 2. DATA IBU ---
            if (str_contains($clean, 'ibu')) {
                if (str_contains($clean, 'nik') || str_contains($clean, 'ktp')) {
                    $map['mother_nik'] = $cIdx;
                } elseif (str_contains($clean, 'tempat lahir') || (str_contains($clean, 'tempat') && !str_contains($clean, 'bekerja'))) {
                    $map['mother_birth_place'] = $cIdx;
                } elseif (str_contains($clean, 'tanggal') || str_contains($clean, 'tgl')) {
                    $map['mother_birth_date'] = $cIdx;
                } elseif (str_contains($clean, 'agama')) {
                    $map['mother_religion'] = $cIdx;
                } elseif (str_contains($clean, 'alamat') && (str_contains($clean, 'instansi') || str_contains($clean, 'kantor'))) {
                    $map['mother_company_address'] = $cIdx;
                } elseif ((str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'hp')) && (str_contains($clean, 'instansi') || str_contains($clean, 'kantor'))) {
                    $map['mother_company_phone'] = $cIdx;
                } elseif (str_contains($clean, 'instansi') || str_contains($clean, 'tempat bekerja') || str_contains($clean, 'perusahaan')) {
                    $map['mother_company'] = $cIdx;
                } elseif (str_contains($clean, 'handphone') || str_contains($clean, 'hp') || str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'wa') || str_contains($clean, 'ponsel')) {
                    $map['mother_phone'] = $cIdx;
                } elseif (str_contains($clean, 'pendidikan') || str_contains($clean, 'lulusan')) {
                    $map['mother_education'] = $cIdx;
                } elseif (str_contains($clean, 'penghasilan') || str_contains($clean, 'gaji') || str_contains($clean, 'pendapatan')) {
                    $map['mother_income'] = $cIdx;
                } elseif (str_contains($clean, 'email')) {
                    $map['mother_email'] = $cIdx;
                } elseif (str_contains($clean, 'pekerjaan') || str_contains($clean, 'profesi') || str_contains($clean, 'kerja')) {
                    $map['mother_job'] = $cIdx;
                } elseif (str_contains($clean, 'nama')) {
                    $map['mother_name'] = $cIdx;
                }
                continue;
            }

            // --- 3. DATA WALI ---
            if (str_contains($clean, 'wali')) {
                if (str_contains($clean, 'hubungan')) {
                    $map['guardian_relation'] = $cIdx;
                } elseif (str_contains($clean, 'tempat lahir') || (str_contains($clean, 'tempat') && !str_contains($clean, 'tinggal'))) {
                    $map['guardian_birth_place'] = $cIdx;
                } elseif (str_contains($clean, 'tanggal') || str_contains($clean, 'tgl')) {
                    $map['guardian_birth_date'] = $cIdx;
                } elseif (str_contains($clean, 'agama')) {
                    $map['guardian_religion'] = $cIdx;
                } elseif (str_contains($clean, 'alamat')) {
                    $map['guardian_address'] = $cIdx;
                } elseif (str_contains($clean, 'handphone') || str_contains($clean, 'hp') || str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'wa') || str_contains($clean, 'ponsel')) {
                    $map['guardian_phone'] = $cIdx;
                } elseif (str_contains($clean, 'pendidikan') || str_contains($clean, 'lulusan')) {
                    $map['guardian_education'] = $cIdx;
                } elseif (str_contains($clean, 'pekerjaan') || str_contains($clean, 'profesi') || str_contains($clean, 'kerja')) {
                    $map['guardian_job'] = $cIdx;
                } elseif (str_contains($clean, 'nama')) {
                    $map['guardian_name'] = $cIdx;
                }
                continue;
            }

            // --- 4. IDENTITAS & LEGALITAS SISWA ---
            if (str_contains($clean, 'nisn')) {
                $map['nisn'] = $cIdx;
            } elseif ((preg_match('/\bnis\b/', $clean) || str_starts_with($clean, 'nis')) && !str_contains($clean, 'jenis') && !str_contains($clean, 'teknis')) {
                $map['nis'] = $cIdx;
            } elseif (str_contains($clean, 'nama siswa') || str_contains($clean, 'nama lengkap') || (str_contains($clean, 'nama') && (str_contains($clean, 'peserta didik') || str_contains($clean, 'murid'))) || $clean === 'nama') {
                $map['full_name'] = $cIdx;
            } elseif (str_contains($clean, 'panggilan') || str_contains($clean, 'nickname')) {
                $map['nickname'] = $cIdx;
            } elseif ($clean === 'sex' || $clean === 'jk' || $clean === 'l/p' || $clean === 'gender' || str_contains($clean, 'jenis kelamin') || str_contains($clean, 'kelamin')) {
                $map['gender'] = $cIdx;
            } elseif (str_contains($clean, 'tempat lahir')) {
                $map['birth_place'] = $cIdx;
            } elseif (str_contains($clean, 'tanggal lahir') || str_contains($clean, 'tgl lahir')) {
                $map['birth_date'] = $cIdx;
            } elseif (str_contains($clean, 'no kk') || str_contains($clean, 'kartu keluarga') || $clean === 'kk') {
                $map['no_kk'] = $cIdx;
            } elseif (str_contains($clean, 'akta') || str_contains($clean, 'akte')) {
                $map['birth_certificate_no'] = $cIdx;
            } elseif (preg_match('/\bnik\b/', $clean) || str_starts_with($clean, 'nik') || str_contains($clean, 'nik anak') || str_contains($clean, 'nik siswa')) {
                $map['nik'] = $cIdx;
            } elseif (str_contains($clean, 'kewarganegaraan') || str_contains($clean, 'warga negara') || str_contains($clean, 'citizenship')) {
                $map['citizenship'] = $cIdx;
            } elseif (str_contains($clean, 'agama') || str_contains($clean, 'kepercayaan')) {
                $map['religion'] = $cIdx;
            }

            // --- 5. INKLUSI & PDBK ---
            elseif (str_contains($clean, 'ketunaan') || (str_contains($clean, 'kebutuhan khusus') && !str_contains($clean, 'catatan'))) {
                $map['special_needs_type'] = $cIdx;
            } elseif (str_contains($clean, 'catatan kebutuhan') || (str_contains($clean, 'kebutuhan') && str_contains($clean, 'catatan'))) {
                $map['special_needs_notes'] = $cIdx;
            } elseif (str_contains($clean, 'jenis peserta didik') || str_contains($clean, 'tipe siswa') || str_contains($clean, 'pdbk') || str_contains($clean, 'inklusi') || str_contains($clean, 'reguler')) {
                $map['student_type'] = $cIdx;
            }

            // --- 6. ROMBEL & TAPEL ---
            elseif (str_contains($clean, 'grade') || str_contains($clean, 'kode rombel') || str_contains($clean, 'kode kelas') || $clean === 'kelas') {
                $map['code'] = $cIdx;
                $hasSeparateRombel = true;
            } elseif (str_contains($clean, 'class name') || str_contains($clean, 'nama kelas') || str_contains($clean, 'julukan') || str_contains($clean, 'classname')) {
                $map['class_name'] = $cIdx;
                $hasSeparateRombel = true;
            } elseif (str_contains($clean, 'rombel') && !$hasSeparateRombel) {
                $map['rombel_combined'] = $cIdx;
            } elseif (str_contains($clean, 'tahun') || str_contains($clean, 'tapel') || str_contains($clean, 'ajaran')) {
                $map['academic_year'] = $cIdx;
            } elseif (str_contains($clean, 'tanggal diterima') || str_contains($clean, 'tanggal masuk') || str_contains($clean, 'tgl masuk') || str_contains($clean, 'tgl diterima')) {
                $map['enrolled_date'] = $cIdx;
            }

            // --- 7. ALAMAT & DOMISILI SISWA ---
            elseif ((str_contains($clean, 'alamat rumah') || str_contains($clean, 'alamat tempat tinggal') || str_contains($clean, 'alamat') || str_contains($clean, 'domisili') || str_contains($clean, 'jalan')) && !str_contains($clean, 'tk') && !str_contains($clean, 'sekolah') && !str_contains($clean, 'kantor')) {
                $map['address'] = $cIdx;
            } elseif (!isset($map['rt']) && ($clean === 'rt' || str_starts_with($clean, 'rt ') || str_ends_with($clean, ' rt'))) {
                $map['rt'] = $cIdx;
            } elseif (!isset($map['rw']) && ($clean === 'rw' || str_starts_with($clean, 'rw ') || str_ends_with($clean, ' rw'))) {
                $map['rw'] = $cIdx;
            } elseif (!isset($map['village']) && (str_contains($clean, 'kelurahan') || str_contains($clean, 'desa'))) {
                $map['village'] = $cIdx;
            } elseif (str_contains($clean, 'sebaran kecamatan') || str_contains($clean, 'kategori wilayah') || str_contains($clean, 'dalam kota') || str_contains($clean, 'luar kota')) {
                $map['district_category'] = $cIdx;
            } elseif (!isset($map['district']) && str_contains($clean, 'kecamatan')) {
                $map['district'] = $cIdx;
            } elseif (!isset($map['city']) && (str_contains($clean, 'kabupaten') || str_contains($clean, 'kota') || str_contains($clean, 'kab'))) {
                $map['city'] = $cIdx;
            } elseif (str_contains($clean, 'provinsi') || str_contains($clean, 'propinsi')) {
                $map['province'] = $cIdx;
            } elseif (str_contains($clean, 'kode pos') || str_contains($clean, 'kodepos') || str_contains($clean, 'pos')) {
                $map['postal_code'] = $cIdx;
            } elseif (str_contains($clean, 'status tempat tinggal') || str_contains($clean, 'tinggal bersama') || str_contains($clean, 'status tinggal')) {
                $map['residence_status'] = $cIdx;
            } elseif (str_contains($clean, 'jarak')) {
                $map['distance_to_school'] = $cIdx;
            } elseif (str_contains($clean, 'telepon rumah') || str_contains($clean, 'telp rumah') || str_contains($clean, 'tlp rumah')) {
                $map['home_phone'] = $cIdx;
            } elseif (str_contains($clean, 'handphone') || str_contains($clean, 'whatsapp') || str_contains($clean, 'hp ortu') || str_contains($clean, 'no wa')) {
                $map['parent_phone'] = $cIdx;
            }

            // --- 8. KELUARGA & SAUDARA ---
            elseif (str_contains($clean, 'anak ke')) {
                $map['child_number'] = $cIdx;
            } elseif (str_contains($clean, 'saudara tiri') || str_contains($clean, 'tiri')) {
                $map['step_siblings_count'] = $cIdx;
            } elseif (str_contains($clean, 'saudara angkat') || str_contains($clean, 'angkat')) {
                $map['adoptive_siblings_count'] = $cIdx;
            } elseif (str_contains($clean, 'saudara kandung') || str_contains($clean, 'kandung') || str_contains($clean, 'saudara')) {
                $map['siblings_count'] = $cIdx;
            } elseif (str_contains($clean, 'bahasa')) {
                $map['home_language'] = $cIdx;
            }

            // --- 9. KESEHATAN & UKS ---
            elseif (str_contains($clean, 'gol darah') || str_contains($clean, 'golongan darah') || str_contains($clean, 'darah')) {
                $map['blood_type'] = $cIdx;
            } elseif ($clean === 'bb' || str_contains($clean, 'berat badan') || str_starts_with($clean, 'berat')) {
                $map['weight'] = $cIdx;
            } elseif ($clean === 'tb' || str_contains($clean, 'tinggi badan') || str_starts_with($clean, 'tinggi')) {
                $map['height'] = $cIdx;
            } elseif (str_contains($clean, 'penyakit berat') || str_contains($clean, 'riwayat penyakit') || str_contains($clean, 'berat/kronis')) {
                $map['severe_disease_history'] = $cIdx;
            } elseif (str_contains($clean, 'sering diderita') || str_contains($clean, 'penyakit sering') || str_contains($clean, 'keluhan')) {
                $map['frequent_disease'] = $cIdx;
            }

            // --- 10. ASAL SEKOLAH & LAINNYA ---
            elseif (str_contains($clean, 'asal peserta didik') || str_contains($clean, 'kategori asal') || str_contains($clean, 'jalur masuk')) {
                $map['origin_category'] = $cIdx;
            } elseif (str_contains($clean, 'alamat tk') || (str_contains($clean, 'asal') && str_contains($clean, 'alamat')) || (str_contains($clean, 'sekolah') && str_contains($clean, 'alamat'))) {
                $map['previous_school_address'] = $cIdx;
            } elseif (str_contains($clean, 'nama tk') || str_contains($clean, 'asal sekolah') || str_contains($clean, 'sekolah asal') || str_contains($clean, 'sekolah sebelumnya')) {
                $map['previous_school'] = $cIdx;
            } elseif (str_contains($clean, 'sttb') || str_contains($clean, 'ijazah')) {
                $map['sttb_number_date'] = $cIdx;
            } elseif (str_contains($clean, 'catatan') && !str_contains($clean, 'kebutuhan')) {
                $map['notes'] = $cIdx;
            } elseif (str_contains($clean, 'status') && !str_contains($clean, 'hubungan') && !str_contains($clean, 'tinggal') && !str_contains($clean, 'wali')) {
                $map['status'] = $cIdx;
            }
        }

        return $map;
    }
}

