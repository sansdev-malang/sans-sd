<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        $schoolUnit = config('app.school_unit', 'smp');

        $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
        $user = auth()->user();

        try {
            $apiParams = [
                'month' => $month,
                'unit_id' => strtolower($schoolUnit)
            ];

            $monthCarbon = \Carbon\Carbon::createFromFormat('Y-m', $month);
            $previousMonth = $monthCarbon->copy()->subMonthNoOverflow()->format('Y-m');

            if ($user && $user->role === 'employee') {
                $apiParams['start_date'] = $monthCarbon->copy()->startOfMonth()->format('Y-m-d');
                $apiParams['end_date'] = $monthCarbon->copy()->endOfMonth()->format('Y-m-d');
            }

            $response = \Illuminate\Support\Facades\Http::get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', $apiParams);
            
            $json = $response->json();
            $reports = collect($json['data'] ?? []);
            $startDate = \Carbon\Carbon::parse($json['start_date'] ?? date('Y-m-d'));
            $endDate = \Carbon\Carbon::parse($json['end_date'] ?? date('Y-m-d'));

            if ($user && $user->role === 'employee' && $user->employee_id) {
                $previousResponse = \Illuminate\Support\Facades\Http::get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                    'month' => $previousMonth,
                    'unit_id' => strtolower($schoolUnit),
                    'start_date' => \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->startOfMonth()->format('Y-m-d'),
                    'end_date' => \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->endOfMonth()->format('Y-m-d'),
                ]);

                $previousJson = $previousResponse->json();
                $previousReports = collect($previousJson['data'] ?? []);
                $previousReport = $previousReports->first(function ($item) use ($user) {
                    return ($item['employee']['id'] ?? 0) == $user->employee_id;
                });

                $currentReport = $reports->first(function ($item) use ($user) {
                    return ($item['employee']['id'] ?? 0) == $user->employee_id;
                });

                if ($currentReport && $previousReport) {
                    $currentDetails = $currentReport['daily_details'] ?? [];
                    $previousDetails = $previousReport['daily_details'] ?? [];
                    $currentReport['daily_details'] = $previousDetails + $currentDetails;
                    $reports = collect([$currentReport]);
                }
            }

            if ($user && $user->role === 'employee' && $user->employee_id) {
                // If it's a regular employee, only show their own report
                $reports = $reports->filter(function ($item) use ($user) {
                    return ($item['employee']['id'] ?? 0) == $user->employee_id;
                });

                // Fetch bonus reports for both current and previous month from central HRD
                $bonusResponse = \Illuminate\Support\Facades\Http::get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                    'month' => $month,
                    'unit_id' => strtolower($schoolUnit)
                ]);
                $bonusJson = $bonusResponse->json();
                $bonusReports = collect($bonusJson['data'] ?? []);

                $prevBonusResponse = \Illuminate\Support\Facades\Http::get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                    'month' => $previousMonth,
                    'unit_id' => strtolower($schoolUnit)
                ]);
                $prevBonusJson = $prevBonusResponse->json();
                $previousBonusReports = collect($prevBonusJson['data'] ?? []);

                $reports = $reports->map(function ($report) use ($bonusReports, $previousBonusReports) {
                    $empId = $report['employee']['id'] ?? 0;
                    
                    $currentBonus = $bonusReports->first(function ($br) use ($empId) {
                        return ($br['employee']['id'] ?? 0) == $empId;
                    });
                    
                    $prevBonus = $previousBonusReports->first(function ($br) use ($empId) {
                        return ($br['employee']['id'] ?? 0) == $empId;
                    });
                    
                    $bonusDetails = [];
                    if ($prevBonus && isset($prevBonus['daily_details'])) {
                        $bonusDetails = $prevBonus['daily_details'];
                    }
                    if ($currentBonus && isset($currentBonus['daily_details'])) {
                        $bonusDetails = $bonusDetails + $currentBonus['daily_details'];
                    }

                    if (isset($report['daily_details'])) {
                        $details = $report['daily_details'];
                        foreach ($details as $dateStr => &$detail) {
                            $bonusDetail = $bonusDetails[$dateStr] ?? null;
                            $detail['calculated_bonus'] = $bonusDetail ? (float)($bonusDetail['bonus_nominal'] ?? 0.00) : 0.00;
                        }
                        $report['daily_details'] = $details;
                    }
                    return $report;
                });
            } else {
                // Filter Search
                if (!empty($search)) {
                    $reports = $reports->filter(function ($item) use ($search) {
                        $name = $item['employee']['name'] ?? '';
                        $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                        return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                    });
                }
            }

            // Pagination
            if ($perPage !== 'all') {
                $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
                $currentItems = $reports->slice(($currentPage - 1) * $perPage, $perPage)->values();
                $paginatedReports = new \Illuminate\Pagination\LengthAwarePaginator(
                    $currentItems,
                    $reports->count(),
                    $perPage,
                    $currentPage,
                    ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
                );
                $reports = $paginatedReports;
            } else {
                $reports = $reports->values();
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memuat matriks absensi dari HRD: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            $reports = collect([]);
            $startDate = \Carbon\Carbon::now();
            $endDate = \Carbon\Carbon::now();
            session()->flash('error', 'Gagal memuat matriks absensi dari HRD: ' . $e->getMessage());
        }

        if ($user && $user->role === 'employee' && $user->employee_id) {
            return view('admin.attendances.calendar', compact('reports', 'month', 'search', 'perPage', 'startDate', 'endDate'));
        }

        return view('admin.attendances.index', compact('reports', 'month', 'search', 'perPage', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $month = $request->query('month', date('Y-m'));
        $search = $request->input('search');
        $format = $request->input('format', 'excel');
        $schoolUnit = config('app.school_unit', 'smp');

        $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));

        try {
            $response = \Illuminate\Support\Facades\Http::get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                'month' => $month,
                'unit_id' => strtolower($schoolUnit)
            ]);
            $json = $response->json();
            $reportsData = $json['data'] ?? [];
            $startDate = \Carbon\Carbon::parse($json['start_date'] ?? date('Y-m-d'));
            $endDate = \Carbon\Carbon::parse($json['end_date'] ?? date('Y-m-d'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat data dari HRD: ' . $e->getMessage());
        }

                $reports = collect($reportsData);
        
        // Apply Role-based filtering
        $user = auth()->user();
        if ($user && $user->role === 'employee' && $user->employee_id) {
            // If it's a regular employee, only show their own report
            $reports = $reports->filter(function ($item) use ($user) {
                return ($item['employee']['id'] ?? 0) == $user->employee_id;
            });
        } else {
            if (!empty($search)) {
                $reports = $reports->filter(function ($item) use ($search) {
                    $name = $item['employee']['name'] ?? '';
                    $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                    return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                });
            }
        }
        
        $reports = $reports->values()->toArray();

        $periodeStr = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        $searchStr = !empty($search) ? '_Pencarian_' . preg_replace('/[^A-Za-z0-9]/', '', $search) : '';
        $baseFileName = 'Matriks_Absensi_Unit_' . strtoupper($schoolUnit) . '_' . $month . $searchStr;

        $start = $startDate->copy();
        $end = clone $endDate;
        $dates = [];
        while($start <= $end) {
            $dates[] = $start->copy();
            $start->addDay();
        }

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.attendances.export-pdf', compact('reports', 'periodeStr', 'dates', 'schoolUnit'))
                ->setPaper('a4', 'landscape');
            return $pdf->download($baseFileName . ".pdf");
        }

        // Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Absensi');

        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NAMA PEGAWAI');
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);

        $colIndex = 3;
        foreach ($dates as $dateObj) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '1', $dateObj->translatedFormat('D') . ", " . $dateObj->format('d/M'));
            $sheet->getColumnDimension($colLetter)->setWidth(12);
            if ($dateObj->isSunday()) {
                $sheet->getStyle($colLetter . '1')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
            }
            $colIndex++;
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex - 1);
        $headerRange = 'A1:' . $lastColLetter . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');

        $sheet->freezePane('C2');

        $row = 2;
        $no = 1;
        foreach ($reports as $report) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $report['employee']['name'] ?? '-');
            
            $colIndex = 3;
            foreach ($dates as $dateObj) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $dateStr = $dateObj->format('Y-m-d');
                $detail = $report['daily_details'][$dateStr] ?? null;
                
                $cellValue = '-';
                if ($detail) {
                    if ($detail['status'] === 'Hadir') {
                        $in = $detail['check_in'] ?? '-';
                        $out = $detail['check_out'] ?? '-';
                        $cellValue = $in . "\n" . $out;
                        $sheet->getStyle($colLetter . $row)->getAlignment()->setWrapText(true);
                    } elseif ($detail['status'] === 'Alfa') {
                        $cellValue = 'A';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    } elseif ($detail['status'] === 'Cuti/Izin') {
                        $cellValue = $detail['leave_type'] ?? 'IZIN';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE));
                    } elseif ($detail['status'] === 'Libur') {
                        $cellValue = '-';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    } elseif ($detail['status'] === 'Off') {
                        $cellValue = 'OFF';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF9CA3AF'));
                    }
                } else {
                    if ($dateObj->isSunday()) {
                        $cellValue = '-';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    }
                }
                
                $sheet->setCellValue($colLetter . $row, $cellValue);
                if ($cellValue === 'A' || $cellValue === 'L' || $cellValue === '-' || $detail['status'] ?? '' === 'Cuti/Izin') {
                    $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal('center')->setVertical('center');
                }
                $colIndex++;
            }
            $row++;
        }

        $dataRange = 'A1:' . $lastColLetter . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $responseHeaders = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $baseFileName . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, $responseHeaders);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'sometimes|nullable|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $calc = $this->calculateAttendance(
            $validated['employee_id'],
            $validated['date'],
            $validated['clock_in'] ?? null,
            $validated['clock_out'] ?? null,
            $validated['status'] ?? null,
            $validated['notes'] ?? null
        );

        $validated['status'] = $calc['status'];
        $validated['calculated_bonus'] = $calc['calculated_bonus'];

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            $validated
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully.',
                'data' => $attendance,
            ], 201);
        }

        return redirect()->back()->with('success', 'Kehadiran berhasil dicatat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return response()->json([
            'success' => true,
            'data' => $attendance->load('employee'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'sometimes|nullable|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $clockIn = array_key_exists('clock_in', $validated) ? $validated['clock_in'] : $attendance->clock_in;
        $clockOut = array_key_exists('clock_out', $validated) ? $validated['clock_out'] : $attendance->clock_out;
        $status = array_key_exists('status', $validated) ? $validated['status'] : null;

        $calc = $this->calculateAttendance(
            $attendance->employee_id,
            $attendance->date,
            $clockIn,
            $clockOut,
            $status,
            $validated['notes'] ?? $attendance->notes
        );

        $validated['status'] = $calc['status'];
        $validated['calculated_bonus'] = $calc['calculated_bonus'];

        $attendance->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
                'data' => $attendance,
            ]);
        }

        return redirect()->back()->with('success', 'Kehadiran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Log kehadiran berhasil dihapus!');
    }

    /**
     * Recalculate attendance for a date.
     */
    public function recalculate(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $attendances = Attendance::where('date', $date)->get();

        foreach ($attendances as $att) {
            $calc = $this->calculateAttendance(
                $att->employee_id,
                $att->date,
                $att->clock_in,
                $att->clock_out,
                in_array($att->status, ['Sick', 'Leave']) ? $att->status : null,
                $att->notes
            );

            $att->status = $calc['status'];
            $att->calculated_bonus = $calc['calculated_bonus'];
            $att->save();
        }

        return redirect()->back()->with('success', 'Rekalkulasi absensi tanggal ' . $date . ' selesai.');
    }

    /**
     * Dynamic calculations logic.
     */
    public function calculateAttendance($employeeId, $date, $clockIn, $clockOut, $manualStatus = null, $notes = null)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $employee = Employee::findOrFail($employeeId);

        // 1. Check Holiday Adjustment or global holiday
        $isHoliday = false;
        
        $adjustment = \App\Models\HolidayAdjustment::where('adjusted_date', $date)->first();

        if ($adjustment) {
            $isHoliday = true;
        } else {
            $holiday = \App\Models\Holiday::where('original_date', $date)->first();
            if ($holiday) {
                $wasRescheduled = \App\Models\HolidayAdjustment::where('holiday_id', $holiday->id)
                    ->where('original_date', $date)
                    ->exists();
                if (!$wasRescheduled) {
                    $isHoliday = true;
                }
            }
        }

        // 2. Find Assigned Shift or Default
        $activeShiftAssigned = \App\Models\EmployeeWorkingShift::where('employee_id', $employeeId)
            ->where('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $date);
            })->first();

        $shift = null;
        if ($activeShiftAssigned) {
            $shift = \App\Models\WorkingShift::find($activeShiftAssigned->working_shift_id);
        }

        if (!$shift) {
            $shift = \App\Models\WorkingShift::where('code', 'default')->first();
        }

        $shiftDetail = null;
        if ($shift) {
            $shiftDetail = \App\Models\WorkingShiftDetail::where('working_shift_id', $shift->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
        }

        $isOffDay = ($shiftDetail && $shiftDetail->is_off) || $isHoliday;

        // 3. Check Approved Leave Requests
        $leave = \App\Models\LeaveRequest::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('status', 'Approved')
            ->first();

        // 4. Calculate Status and Bonus
        $status = $manualStatus;
        $calculatedBonus = 0.00;

        if ($leave) {
            $getsBonus = $leave->leaveType ? $leave->leaveType->gets_presence_bonus : ($leave->type === 'Dinas');
            $statusCode = $leave->leaveType ? $leave->leaveType->status_code : null;

            if ($statusCode === 'S') {
                $status = 'Sick';
            } else {
                $status = 'Leave';
            }

            if ($getsBonus) {
                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    if ($maxTier) {
                        $calculatedBonus = $maxTier->nominal;
                    }
                }
            } else {
                $calculatedBonus = 0.00;
            }
        } elseif ($isOffDay) {
            if (!$clockIn) {
                $status = 'Off';
                $calculatedBonus = 0.00;
            } else {
                $status = 'Present';
                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    if ($maxTier) {
                        $calculatedBonus = $maxTier->nominal;
                    }
                }
            }
        } else {
            // Work day
            if (!$clockIn) {
                if (!$status || $status === 'Present') {
                    $status = 'Absent';
                }
                $calculatedBonus = 0.00;
            } else {
                $status = 'Present';
                $lateMinutes = 0;

                if ($shiftDetail && $shiftDetail->start_time) {
                    $shiftStart = Carbon::parse($date . ' ' . $shiftDetail->start_time);
                    $actualIn = Carbon::parse($date . ' ' . $clockIn);

                    if ($actualIn->gt($shiftStart)) {
                        $lateMinutes = $actualIn->diffInMinutes($shiftStart);
                        $status = 'Late';
                    }
                }

                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $matchingTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->where('max_late_minutes', '>=', $lateMinutes)
                        ->orderBy('nominal', 'desc')
                        ->first();

                    if ($matchingTier) {
                        $calculatedBonus = $matchingTier->nominal;
                    } else {
                        $calculatedBonus = 0.00;
                    }
                }
            }
        }

        return [
            'status' => $status ?? 'Present',
            'calculated_bonus' => $calculatedBonus,
        ];
    }
}
