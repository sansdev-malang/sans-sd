<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StudentAttendanceController extends Controller
{
    /**
     * Display the Student Attendance dashboard (Daily input & Monthly recap).
     */
    public function index(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        $classrooms = Classroom::with(['homeroomTeacher', 'classLevel'])
            ->where('is_active', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('class_level_id', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Determine default classroom
        $user = auth()->user();
        $defaultClassroomId = null;
        if ($user && $user->employee_id) {
            $assignedClass = $classrooms->firstWhere('homeroom_teacher_id', $user->employee_id);
            if ($assignedClass) {
                $defaultClassroomId = $assignedClass->id;
            }
        }
        if (!$defaultClassroomId && $classrooms->isNotEmpty()) {
            $defaultClassroomId = $classrooms->first()->id;
        }

        $selectedClassroomId = $request->get('classroom_id', $defaultClassroomId);
        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);

        // Date selection
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $carbonDate = Carbon::today();
            $selectedDate = $carbonDate->format('Y-m-d');
        }

        // Active tab: 'daily' or 'monthly'
        $activeTab = $request->get('tab', 'daily');

        // Month & Year for recap
        $selectedMonth = (int) $request->get('month', $carbonDate->month);
        $selectedYear = (int) $request->get('year', $carbonDate->year);

        // Get active academic year
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        // Students in the selected classroom
        $students = collect();
        if ($selectedClassroom) {
            $students = Student::where('status', 'aktif')
                ->where('classroom_id', $selectedClassroom->id)
                ->orderBy('full_name', 'asc')
                ->get();
        }

        // --- 1. DATA TAB INPUT HARIAN ---
        $existingDailyAttendances = collect();
        $dailyStats = [
            'total' => $students->count(),
            'h' => 0,
            's' => 0,
            'i' => 0,
            'a' => 0,
            'recorded' => 0,
            'unrecorded' => $students->count(),
        ];

        if ($selectedClassroom && $students->isNotEmpty()) {
            $existingDailyAttendances = StudentAttendance::where('classroom_id', $selectedClassroom->id)
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $st) {
                if (isset($existingDailyAttendances[$st->id])) {
                    $stStatus = $existingDailyAttendances[$st->id]->status;
                    if ($stStatus === 'H') $dailyStats['h']++;
                    elseif ($stStatus === 'S') $dailyStats['s']++;
                    elseif ($stStatus === 'I') $dailyStats['i']++;
                    elseif ($stStatus === 'A') $dailyStats['a']++;
                    $dailyStats['recorded']++;
                }
            }
            $dailyStats['unrecorded'] = max(0, $dailyStats['total'] - $dailyStats['recorded']);
        }

        // --- 2. DATA TAB REKAP BULANAN ---
        $monthlyRecap = [];
        $monthlyStats = [
            'total_students' => $students->count(),
            'effective_days' => 0,
            'total_h' => 0,
            'total_s' => 0,
            'total_i' => 0,
            'total_a' => 0,
            'avg_attendance_rate' => 0,
        ];

        if ($selectedClassroom && $students->isNotEmpty()) {
            $monthlyRecords = StudentAttendance::where('classroom_id', $selectedClassroom->id)
                ->whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonth)
                ->get();

            // Count distinct effective days recorded
            $effectiveDays = $monthlyRecords->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->unique()->values();
            $monthlyStats['effective_days'] = $effectiveDays->count();

            $totalRatesSum = 0;

            foreach ($students as $st) {
                $stRecords = $monthlyRecords->where('student_id', $st->id);
                $cntH = $stRecords->where('status', 'H')->count();
                $cntS = $stRecords->where('status', 'S')->count();
                $cntI = $stRecords->where('status', 'I')->count();
                $cntA = $stRecords->where('status', 'A')->count();
                $totalPertemuan = $cntH + $cntS + $cntI + $cntA;

                $rate = $totalPertemuan > 0 ? round(($cntH / $totalPertemuan) * 100, 1) : 0;
                $totalRatesSum += $rate;

                $monthlyStats['total_h'] += $cntH;
                $monthlyStats['total_s'] += $cntS;
                $monthlyStats['total_i'] += $cntI;
                $monthlyStats['total_a'] += $cntA;

                $monthlyRecap[] = [
                    'student' => $st,
                    'h' => $cntH,
                    's' => $cntS,
                    'i' => $cntI,
                    'a' => $cntA,
                    'total' => $totalPertemuan,
                    'rate' => $rate,
                ];
            }

            if ($students->count() > 0) {
                $monthlyStats['avg_attendance_rate'] = round($totalRatesSum / $students->count(), 1);
            }
        }

        // Indonesian Month Names
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Available years (5 years window)
        $currentYear = (int) Carbon::now()->year;
        $yearsList = range($currentYear - 3, $currentYear + 2);

        return view('admin.student-attendances.index', compact(
            'classrooms',
            'selectedClassroom',
            'selectedClassroomId',
            'selectedDate',
            'carbonDate',
            'activeTab',
            'selectedMonth',
            'selectedYear',
            'activeAcademicYear',
            'students',
            'existingDailyAttendances',
            'dailyStats',
            'monthlyRecap',
            'monthlyStats',
            'monthNames',
            'yearsList'
        ));
    }

    /**
     * Store or update daily attendance inline form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'date' => 'required|date',
            'attendances' => 'nullable|array',
            'attendances.*.status' => 'nullable|in:H,S,I,A',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $classroomId = $validated['classroom_id'];
        $date = Carbon::parse($validated['date'])->format('Y-m-d');
        $attendancesInput = $request->input('attendances', []);

        $classroom = Classroom::find($classroomId);
        $academicYearId = $classroom?->academic_year_id ?? AcademicYear::where('is_active', true)->value('id');
        $userId = auth()->id();

        $savedCount = 0;

        DB::transaction(function () use ($attendancesInput, $classroomId, $academicYearId, $date, $userId, &$savedCount) {
            foreach ($attendancesInput as $studentId => $item) {
                $status = $item['status'] ?? null;
                $notes = isset($item['notes']) ? trim($item['notes']) : null;

                if (!empty($status)) {
                    StudentAttendance::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'date' => $date,
                        ],
                        [
                            'classroom_id' => $classroomId,
                            'academic_year_id' => $academicYearId,
                            'status' => $status,
                            'notes' => $notes,
                            'recorded_by' => $userId,
                        ]
                    );
                    $savedCount++;
                } else {
                    // If status was cleared, remove the attendance record for this day
                    StudentAttendance::where('student_id', $studentId)
                        ->whereDate('date', $date)
                        ->delete();
                }
            }
        });

        $dateFormatted = Carbon::parse($date)->translatedFormat('l, d F Y');
        $message = "Berhasil menyimpan absensi {$savedCount} murid untuk tanggal {$dateFormatted}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'saved_count' => $savedCount,
            ]);
        }

        return redirect()->route('student-attendances.index', [
            'classroom_id' => $classroomId,
            'date' => $date,
            'tab' => 'daily',
        ])->with('success', $message);
    }

    /**
     * Export Monthly Recap to Excel (.xlsx).
     */
    public function exportMonthlyExcel(Request $request)
    {
        $classroomId = $request->get('classroom_id');
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $classroom = Classroom::with(['homeroomTeacher', 'classLevel'])->findOrFail($classroomId);

        // Fetch students
        $students = Student::where('status', 'aktif')
            ->where('classroom_id', $classroom->id)
            ->orderBy('full_name', 'asc')
            ->get();

        // Fetch monthly records
        $monthlyRecords = StudentAttendance::where('classroom_id', $classroom->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNames[$month] ?? $month;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi ' . substr($monthName, 0, 3));

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI ABSENSI MURID BULANAN');
        $sheet->setCellValue('A2', strtoupper(setting('unit_name', setting('app_name', 'SD ANAK SALEH MALANG'))));
        $sheet->setCellValue('A3', "Kelas / Rombel: {$classroom->name} (" . ($classroom->classLevel?->name ?? 'SMP') . ") | Periode: {$monthName} {$year} | Dicetak: " . date('d/m/Y H:i'));

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->getColor()->setARGB('FF475569');

        // Table Headers
        $headers = ['No', 'NIS', 'Nama Murid', 'Hadir (H)', 'Sakit (S)', 'Izin (I)', 'Alpa (A)', 'Total Pertemuan', '% Kehadiran'];
        $startRow = 5;

        foreach ($headers as $colIndex => $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . $startRow, $h);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A{$startRow}:{$lastCol}{$startRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF10B981'); // Emerald green
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(24);

        $currentRow = $startRow + 1;
        $no = 1;
        $totalH = 0; $totalS = 0; $totalI = 0; $totalA = 0; $totalMeetings = 0;

        foreach ($students as $st) {
            $stRecords = $monthlyRecords->where('student_id', $st->id);
            $cntH = $stRecords->where('status', 'H')->count();
            $cntS = $stRecords->where('status', 'S')->count();
            $cntI = $stRecords->where('status', 'I')->count();
            $cntA = $stRecords->where('status', 'A')->count();
            $tot = $cntH + $cntS + $cntI + $cntA;
            $rate = $tot > 0 ? round(($cntH / $tot) * 100, 1) : 0;

            $totalH += $cntH;
            $totalS += $cntS;
            $totalI += $cntI;
            $totalA += $cntA;
            $totalMeetings += $tot;

            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", $st->nis ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$currentRow}", $st->full_name);
            $sheet->setCellValue("D{$currentRow}", $cntH);
            $sheet->setCellValue("E{$currentRow}", $cntS);
            $sheet->setCellValue("F{$currentRow}", $cntI);
            $sheet->setCellValue("G{$currentRow}", $cntA);
            $sheet->setCellValue("H{$currentRow}", $tot);
            $sheet->setCellValue("I{$currentRow}", $rate . '%');

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $currentRow++;
        }

        // Summary Total Row
        $sheet->setCellValue("A{$currentRow}", 'TOTAL KELAS');
        $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
        $sheet->setCellValue("D{$currentRow}", $totalH);
        $sheet->setCellValue("E{$currentRow}", $totalS);
        $sheet->setCellValue("F{$currentRow}", $totalI);
        $sheet->setCellValue("G{$currentRow}", $totalA);
        $sheet->setCellValue("H{$currentRow}", $totalMeetings);
        $avgRate = $totalMeetings > 0 ? round(($totalH / $totalMeetings) * 100, 1) : 0;
        $sheet->setCellValue("I{$currentRow}", $avgRate . '%');

        $totalRange = "A{$currentRow}:{$lastCol}{$currentRow}";
        $sheet->getStyle($totalRange)->getFont()->setBold(true);
        $sheet->getStyle($totalRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Borders
        $sheet->getStyle("A{$startRow}:{$lastCol}{$currentRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCBD5E1'));

        foreach (range(1, count($headers)) as $colIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        // Teacher signature area
        $signRow = $currentRow + 3;
        $sheet->setCellValue("B{$signRow}", "Mengetahui,\nKepala Sekolah");
        $city = setting('school_city', 'Malang');
        $sheet->setCellValue("G{$signRow}", "{$city}, " . \Carbon\Carbon::now()->translatedFormat('d F Y') . "\nWali Kelas");
        $sheet->getStyle("B{$signRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$signRow}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $principalName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $sheet->setCellValue("B" . ($signRow + 4), $principalName);
        $sheet->getStyle("B" . ($signRow + 4))->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("B" . ($signRow + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $teacherName = $classroom->homeroomTeacher?->name ?? '( ................................ )';
        $sheet->setCellValue("G" . ($signRow + 4), $teacherName);
        $sheet->getStyle("G" . ($signRow + 4))->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("G" . ($signRow + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $cleanClassName = preg_replace('/[^A-Za-z0-9_-]/', '_', $classroom->name);
        $filename = "Rekap_Absensi_{$cleanClassName}_{$monthName}_{$year}.xlsx";

        if ($request->filled('download_token')) {
            if (!headers_sent()) {
                @setcookie('download_token', $request->query('download_token'), time() + 60, '/', '', false, false);
            }
        }

        $response = response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);

        if ($request->filled('download_token')) {
            $response->headers->setCookie(cookie('download_token', $request->query('download_token'), 1, '/', null, false, false));
        }

        return $response;
    }

    /**
     * Printable Monthly Recap view.
     */
    public function printMonthly(Request $request)
    {
        $classroomId = $request->get('classroom_id');
        $month = (int) $request->get('month', Carbon::now()->month);
        $year = (int) $request->get('year', Carbon::now()->year);

        $classroom = Classroom::with(['homeroomTeacher', 'classLevel'])->findOrFail($classroomId);

        $students = Student::where('status', 'aktif')
            ->where('classroom_id', $classroom->id)
            ->orderBy('full_name', 'asc')
            ->get();

        $monthlyRecords = StudentAttendance::where('classroom_id', $classroom->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthName = $monthNames[$month] ?? $month;

        $monthlyRecap = [];
        $totalH = 0; $totalS = 0; $totalI = 0; $totalA = 0; $totalMeetings = 0;

        foreach ($students as $st) {
            $stRecords = $monthlyRecords->where('student_id', $st->id);
            $cntH = $stRecords->where('status', 'H')->count();
            $cntS = $stRecords->where('status', 'S')->count();
            $cntI = $stRecords->where('status', 'I')->count();
            $cntA = $stRecords->where('status', 'A')->count();
            $tot = $cntH + $cntS + $cntI + $cntA;
            $rate = $tot > 0 ? round(($cntH / $tot) * 100, 1) : 0;

            $totalH += $cntH;
            $totalS += $cntS;
            $totalI += $cntI;
            $totalA += $cntA;
            $totalMeetings += $tot;

            $monthlyRecap[] = [
                'student' => $st,
                'h' => $cntH,
                's' => $cntS,
                'i' => $cntI,
                'a' => $cntA,
                'total' => $tot,
                'rate' => $rate,
            ];
        }

        $summaryTotals = [
            'h' => $totalH,
            's' => $totalS,
            'i' => $totalI,
            'a' => $totalA,
            'meetings' => $totalMeetings,
            'avg_rate' => $totalMeetings > 0 ? round(($totalH / $totalMeetings) * 100, 1) : 0,
        ];

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $headmasterName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $headmasterNip = $principal?->niy ?? $principal?->nuptk ?? $principal?->nik ?? setting('headmaster_nip', setting('school_principal_nip', '-'));

        return view('admin.student-attendances.print-monthly', compact(
            'classroom',
            'students',
            'month',
            'year',
            'monthName',
            'monthlyRecap',
            'summaryTotals',
            'headmasterName',
            'headmasterNip'
        ));
    }
}
