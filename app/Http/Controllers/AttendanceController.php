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
        $cutoffDate = (int) \App\Models\Setting::get('payroll_cutoff_date', 26);
        $month = $request->query('month');
        if (empty($month)) {
            $today = now();
            $month = $today->day > $cutoffDate ? $today->copy()->startOfMonth()->addMonth()->format('Y-m') : $today->format('Y-m');
        }
        $search = $request->input('search');
        $perPage = $request->input('per_page', 50);
        $schoolUnit = config('app.school_unit', 'sd');
        $schoolUnitId = config('app.school_unit_id', 2);
        $forceRefresh = $request->boolean('refresh');

        $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
        $user = auth()->user();
        $myActiveShifts = [];

        $monthCarbon = \Carbon\Carbon::parse($month . '-01');
        $isPastMonth = $monthCarbon->copy()->endOfMonth()->isPast();
        $previousMonth = $monthCarbon->copy()->subMonthNoOverflow()->format('Y-m');
        $nextMonth = $monthCarbon->copy()->addMonthNoOverflow()->format('Y-m');

        // Past months: 86400s (1 day). Current month: 20s micro-cache (real-time ADMS sync with fast navigation).
        $cacheTtlCurrent = 20;
        $cacheTtlPast = 86400;

        $matrixCacheKey = "hrd_att_matrix_{$schoolUnitId}_{$month}";
        $prevMatrixCacheKey = "hrd_att_matrix_{$schoolUnitId}_{$previousMonth}";
        $bonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$month}";
        $prevBonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$previousMonth}";
        $nextBonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$nextMonth}";

        if ($forceRefresh) {
            \Illuminate\Support\Facades\Cache::forget($matrixCacheKey);
            \Illuminate\Support\Facades\Cache::forget($prevMatrixCacheKey);
            \Illuminate\Support\Facades\Cache::forget($bonusCacheKey);
            \Illuminate\Support\Facades\Cache::forget($prevBonusCacheKey);
            \Illuminate\Support\Facades\Cache::forget($nextBonusCacheKey);
        }

        try {
            $matrixData = \Illuminate\Support\Facades\Cache::get($matrixCacheKey);
            $prevMatrixData = \Illuminate\Support\Facades\Cache::get($prevMatrixCacheKey);
            $bonusData = \Illuminate\Support\Facades\Cache::get($bonusCacheKey);
            $prevBonusData = \Illuminate\Support\Facades\Cache::get($prevBonusCacheKey);
            $nextBonusData = \Illuminate\Support\Facades\Cache::get($nextBonusCacheKey);

            $needsEmployeeData = $user && $user->role === 'employee' && $user->employee_id;

            // Build list of calls needed
            $poolCalls = [];
            if (!$matrixData) {
                $poolCalls['curr_matrix'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $month) {
                    return $pool->as('curr_matrix')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                        'school_unit_id' => $schoolUnitId,
                        'unit_id' => strtolower($schoolUnit),
                        'month' => $month
                    ]);
                };
            }

            if ($needsEmployeeData) {
                if (!$prevMatrixData) {
                    $poolCalls['prev_matrix'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $previousMonth) {
                        return $pool->as('prev_matrix')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                            'school_unit_id' => $schoolUnitId,
                            'unit_id' => strtolower($schoolUnit),
                            'month' => $previousMonth,
                            'start_date' => \Carbon\Carbon::parse($previousMonth . '-01')->startOfMonth()->format('Y-m-d'),
                            'end_date' => \Carbon\Carbon::parse($previousMonth . '-01')->endOfMonth()->format('Y-m-d'),
                        ]);
                    };
                }
                if (!$bonusData) {
                    $poolCalls['curr_bonus'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $month) {
                        return $pool->as('curr_bonus')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                            'school_unit_id' => $schoolUnitId,
                            'unit_id' => strtolower($schoolUnit),
                            'month' => $month
                        ]);
                    };
                }
                if (!$prevBonusData) {
                    $poolCalls['prev_bonus'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $previousMonth) {
                        return $pool->as('prev_bonus')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                            'school_unit_id' => $schoolUnitId,
                            'unit_id' => strtolower($schoolUnit),
                            'month' => $previousMonth
                        ]);
                    };
                }
                if (!$nextBonusData) {
                    $poolCalls['next_bonus'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $nextMonth) {
                        return $pool->as('next_bonus')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                            'school_unit_id' => $schoolUnitId,
                            'unit_id' => strtolower($schoolUnit),
                            'month' => $nextMonth
                        ]);
                    };
                }
            }

            // Execute missing calls concurrently in parallel via Http::pool
            if (!empty($poolCalls)) {
                $responses = \Illuminate\Support\Facades\Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($poolCalls) {
                    $callList = [];
                    foreach ($poolCalls as $call) {
                        $callList[] = $call($pool);
                    }
                    return $callList;
                });

                if (isset($responses['curr_matrix']) && $responses['curr_matrix'] instanceof \Illuminate\Http\Client\Response && $responses['curr_matrix']->successful()) {
                    $matrixData = $responses['curr_matrix']->json();
                    \Illuminate\Support\Facades\Cache::put($matrixCacheKey, $matrixData, $isPastMonth ? $cacheTtlPast : $cacheTtlCurrent);
                    \Illuminate\Support\Facades\Cache::put($matrixCacheKey . '_stale', $matrixData, 604800);
                }
                if (isset($responses['prev_matrix']) && $responses['prev_matrix'] instanceof \Illuminate\Http\Client\Response && $responses['prev_matrix']->successful()) {
                    $prevMatrixData = $responses['prev_matrix']->json();
                    \Illuminate\Support\Facades\Cache::put($prevMatrixCacheKey, $prevMatrixData, $cacheTtlPast);
                    \Illuminate\Support\Facades\Cache::put($prevMatrixCacheKey . '_stale', $prevMatrixData, 604800);
                }
                if (isset($responses['curr_bonus']) && $responses['curr_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['curr_bonus']->successful()) {
                    $bonusData = $responses['curr_bonus']->json();
                    \Illuminate\Support\Facades\Cache::put($bonusCacheKey, $bonusData, $isPastMonth ? $cacheTtlPast : $cacheTtlCurrent);
                    \Illuminate\Support\Facades\Cache::put($bonusCacheKey . '_stale', $bonusData, 604800);
                }
                if (isset($responses['prev_bonus']) && $responses['prev_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['prev_bonus']->successful()) {
                    $prevBonusData = $responses['prev_bonus']->json();
                    \Illuminate\Support\Facades\Cache::put($prevBonusCacheKey, $prevBonusData, $cacheTtlPast);
                    \Illuminate\Support\Facades\Cache::put($prevBonusCacheKey . '_stale', $prevBonusData, 604800);
                }
                if (isset($responses['next_bonus']) && $responses['next_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['next_bonus']->successful()) {
                    $nextBonusData = $responses['next_bonus']->json();
                    \Illuminate\Support\Facades\Cache::put($nextBonusCacheKey, $nextBonusData, $cacheTtlCurrent);
                    \Illuminate\Support\Facades\Cache::put($nextBonusCacheKey . '_stale', $nextBonusData, 604800);
                }
            }

            // Fallback to stale cache if null
            if (!$matrixData) $matrixData = \Illuminate\Support\Facades\Cache::get($matrixCacheKey . '_stale');
            if (!$prevMatrixData) $prevMatrixData = \Illuminate\Support\Facades\Cache::get($prevMatrixCacheKey . '_stale');
            if (!$bonusData) $bonusData = \Illuminate\Support\Facades\Cache::get($bonusCacheKey . '_stale');
            if (!$prevBonusData) $prevBonusData = \Illuminate\Support\Facades\Cache::get($prevBonusCacheKey . '_stale');
            if (!$nextBonusData) $nextBonusData = \Illuminate\Support\Facades\Cache::get($nextBonusCacheKey . '_stale');

            if (isset($matrixData['cutoff_date'])) {
                \App\Models\Setting::set('payroll_cutoff_date', $matrixData['cutoff_date']);
            }
            $reports = collect($matrixData['data'] ?? []);
            $startDate = \Carbon\Carbon::parse($matrixData['start_date'] ?? ($month . '-01'));
            $endDate = \Carbon\Carbon::parse($matrixData['end_date'] ?? $monthCarbon->copy()->endOfMonth()->format('Y-m-d'));

            if ($needsEmployeeData) {
                $previousReports = collect($prevMatrixData['data'] ?? []);
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
                } elseif ($currentReport) {
                    $reports = collect([$currentReport]);
                }

                $bonusReports = collect($bonusData['data'] ?? []);
                $previousBonusReports = collect($prevBonusData['data'] ?? []);
                $nextBonusReports = collect($nextBonusData['data'] ?? []);

                $empId = $user->employee_id;
                $currentBonus = $bonusReports->first(function ($br) use ($empId) {
                    return ($br['employee']['id'] ?? 0) == $empId;
                });
                if ($currentBonus && isset($currentBonus['active_shifts'])) {
                    $myActiveShifts = $currentBonus['active_shifts'];
                }

                $reports = $reports->map(function ($report) use ($bonusReports, $previousBonusReports, $nextBonusReports) {
                    $empId = $report['employee']['id'] ?? 0;

                    $currentBonus = $bonusReports->first(function ($br) use ($empId) {
                        return ($br['employee']['id'] ?? 0) == $empId;
                    });

                    $prevBonus = $previousBonusReports->first(function ($br) use ($empId) {
                        return ($br['employee']['id'] ?? 0) == $empId;
                    });

                    $nextBonus = $nextBonusReports->first(function ($br) use ($empId) {
                        return ($br['employee']['id'] ?? 0) == $empId;
                    });

                    $bonusDetails = [];
                    if ($prevBonus && isset($prevBonus['daily_details'])) {
                        $bonusDetails = $prevBonus['daily_details'];
                    }
                    if ($currentBonus && isset($currentBonus['daily_details'])) {
                        $bonusDetails = $bonusDetails + $currentBonus['daily_details'];
                    }
                    if ($nextBonus && isset($nextBonus['daily_details'])) {
                        $bonusDetails = $bonusDetails + $nextBonus['daily_details'];
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
            }

            // Extract unique positions from local database for filtering (only for admin/headmaster)
            $positions = collect();
            $position = $request->input('position');

            if (!$needsEmployeeData) {
                $positions = \App\Models\Employee::whereNotNull('position')
                    ->where('position', '!=', '')
                    ->distinct()
                    ->pluck('position')
                    ->sort()
                    ->values();

                // Filter Search
                if (!empty($search)) {
                    $reports = $reports->filter(function ($item) use ($search) {
                        $name = $item['employee']['name'] ?? '';
                        $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                        return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                    });
                }

                // Filter Position
                if (!empty($position)) {
                    $reports = $reports->filter(function ($item) use ($position) {
                        $empPos = $item['employee']['position'] ?? $item['employee']['subject_position'] ?? '';
                        return $empPos === $position;
                    });
                }
            }

            // Fallback to local DB if reports is empty
            if ($reports->isEmpty()) {
                $reports = $this->buildLocalMatrixFallback($monthCarbon, $user, $search, $position);
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
            \Illuminate\Support\Facades\Log::warning('Fallback matriks absensi lokal: ' . $e->getMessage());
            $reports = $this->buildLocalMatrixFallback($monthCarbon, $user, $search, $position ?? null);
            $startDate = $monthCarbon->copy()->startOfMonth();
            $endDate = $monthCarbon->copy()->endOfMonth();
            $positions = \App\Models\Employee::whereNotNull('position')->distinct()->pluck('position')->sort()->values();
            $position = null;
        }

        if ($user && $user->role === 'employee' && $user->employee_id) {
            return view('admin.attendances.calendar', compact('reports', 'month', 'search', 'perPage', 'startDate', 'endDate', 'myActiveShifts'));
        }

        return view('admin.attendances.index', compact('reports', 'month', 'search', 'perPage', 'startDate', 'endDate', 'positions', 'position'));
    }

    /**
     * Local database fallback builder when HRD central aggregator is unreachable
     */
    protected function buildLocalMatrixFallback(Carbon $monthCarbon, $user, $search = null, $position = null)
    {
        $start = $monthCarbon->copy()->startOfMonth();
        $end = $monthCarbon->copy()->endOfMonth();

        $empQuery = \App\Models\Employee::query();
        if ($user && $user->role === 'employee' && $user->employee_id) {
            $empQuery->where('id', $user->employee_id);
        } else {
            if (!empty($search)) {
                $empQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%")
                      ->orWhere('nuptk', 'like', "%{$search}%");
                });
            }
            if (!empty($position)) {
                $empQuery->where('position', $position);
            }
        }

        $employees = $empQuery->orderBy('name')->get();
        $empIds = $employees->pluck('id')->toArray();

        $attendances = \App\Models\Attendance::whereIn('employee_id', $empIds)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->groupBy('employee_id');

        $result = collect();

        foreach ($employees as $emp) {
            $empAtts = $attendances->get($emp->id, collect())->keyBy(function($att) {
                return \Carbon\Carbon::parse($att->date)->format('Y-m-d');
            });

            $dailyDetails = [];
            $cur = $start->copy();
            while ($cur <= $end) {
                $dStr = $cur->format('Y-m-d');
                $att = $empAtts->get($dStr);

                $status = 'Off';
                if ($cur->dayOfWeek >= 1 && $cur->dayOfWeek <= 5) {
                    $status = $cur->isFuture() ? 'Pending' : 'Alpha';
                }
                $checkIn = null;
                $checkOut = null;
                $isLate = false;
                $lateMinutes = 0;

                if ($att) {
                    $status = $att->status ?? 'Hadir';
                    $checkIn = $att->clock_in ? substr($att->clock_in, 0, 5) : null;
                    $checkOut = $att->clock_out ? substr($att->clock_out, 0, 5) : null;
                    if ($checkIn && $checkIn > '07:05') {
                        $isLate = true;
                        $lateMinutes = (int) ((\Carbon\Carbon::parse($att->clock_in)->diffInSeconds(\Carbon\Carbon::parse('07:00:00'))) / 60);
                    }
                }

                $dailyDetails[$dStr] = [
                    'date' => $dStr,
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'is_late' => $isLate,
                    'late_minutes' => $lateMinutes,
                    'calculated_bonus' => 0.00,
                    'shift_name' => 'Shift Reguler',
                    'shift_start' => '07:00',
                    'shift_end' => '15:30'
                ];

                $cur->addDay();
            }

            $result->push([
                'employee' => [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'position' => $emp->position ?? '-',
                    'photo' => $emp->photo ?? null,
                ],
                'daily_details' => $dailyDetails,
            ]);
        }

        return $result;
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $month = $request->query('month', date('Y-m'));
        $search = $request->input('search');
        $position = $request->input('position');
        $format = $request->input('format', 'excel');
        $schoolUnit = config('app.school_unit', 'sd');
        $schoolUnitId = config('app.school_unit_id', 2);

        $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
        $matrixCacheKey = "hrd_att_matrix_{$schoolUnitId}_{$month}";

        $json = \Illuminate\Support\Facades\Cache::get($matrixCacheKey);

        if (!$json) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(15.0)->withHeaders([
                    'X-API-TOKEN' => config('app.hrd_api_token')
                ])->get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                    'school_unit_id' => $schoolUnitId,
                    'month' => $month,
                    'unit_id' => strtolower($schoolUnit)
                ]);
                if ($response->successful()) {
                    $json = $response->json();
                    $monthCarbon = \Carbon\Carbon::parse($month . '-01');
                    $isPastMonth = $monthCarbon->copy()->endOfMonth()->isPast();
                    \Illuminate\Support\Facades\Cache::put($matrixCacheKey, $json, $isPastMonth ? 86400 : 20);
                } else {
                    $json = \Illuminate\Support\Facades\Cache::get($matrixCacheKey . '_stale');
                }
            } catch (\Exception $e) {
                $json = \Illuminate\Support\Facades\Cache::get($matrixCacheKey . '_stale');
                if (!$json) {
                    return back()->with('error', 'Gagal memuat data dari HRD: ' . $e->getMessage());
                }
            }
        }

        $reportsData = $json['data'] ?? [];
        $startDate = \Carbon\Carbon::parse($json['start_date'] ?? date('Y-m-d'));
        $endDate = \Carbon\Carbon::parse($json['end_date'] ?? date('Y-m-d'));

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

            if (!empty($position)) {
                $reports = $reports->filter(function ($item) use ($position) {
                    $empPos = $item['employee']['position'] ?? $item['employee']['subject_position'] ?? '';
                    return $empPos === $position;
                });
            }
        }

        $reports = $reports->values()->toArray();

        $periodeStr = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        $searchStr = !empty($search) ? '_Pencarian_' . preg_replace('/[^A-Za-z0-9]/', '', $search) : '';
        $posStr = !empty($position) ? '_Jabatan_' . preg_replace('/[^A-Za-z0-9]/', '', $position) : '';
        $baseFileName = 'Matriks_Absensi_Unit_' . strtoupper($schoolUnit) . '_' . $month . $searchStr . $posStr;

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
                    $sheet->getStyle($colLetter . $row)->getAlignment()->setWrapText(true)->setHorizontal('center')->setVertical('center');

                    if ($detail['status'] === 'Hadir') {
                        $in = $detail['check_in'] ?? '-';
                        $out = $detail['check_out'] ?? '-';
                        if (!empty($detail['pending_leave'])) {
                            $cellValue = $in . "\n" . $detail['pending_leave']['leave_code'] . "\n" . $out;
                        } else {
                            $cellValue = $in . "\n" . $out;
                        }
                    } elseif ($detail['status'] === 'Alfa') {
                        $cellValue = 'A';
                        if (!empty($detail['pending_leave'])) {
                            $cellValue = "A\n" . $detail['pending_leave']['leave_code'];
                        }
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    } elseif ($detail['status'] === 'Cuti/Izin') {
                        $leaveCode = $detail['leave_code'] ?? 'I';
                        $isPending = !empty($detail['is_pending']);
                        $in = $detail['check_in'] ?? null;
                        $out = $detail['check_out'] ?? null;

                        if ($in || $out) {
                            $cellValue = ($in ?: '-') . "\n" . $leaveCode . ($isPending ? ' (P)' : '') . "\n" . ($out ?: '-');
                        } else {
                            $cellValue = $leaveCode . ($isPending ? ' (P)' : '');
                        }

                        $excelColorMap = [
                            'S' => 'FFE28743', // Warm Amber
                            'I' => 'FF8A2BE2', // Purple
                            'C' => 'FF1F75FE', // Blue
                            'H' => 'FF10B981', // Emerald/Green
                        ];
                        $colorHex = $excelColorMap[$leaveCode] ?? 'FF1F75FE';
                        $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($colorHex));
                        if ($isPending) {
                            $sheet->getStyle($colLetter . $row)->getFont()->setItalic(true);
                        }
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

        if ($clockIn) {
            // Priority 1: Actual Fingerprint Override (Employee actually attended work)
            $status = 'Present';
            $lateMinutes = 0;
            $hasExcusedBonus = false;

            if ($leave && ($leave->leaveType ? $leave->leaveType->gets_presence_bonus : ($leave->type === 'Dinas'))) {
                $hasExcusedBonus = true;
            }

            if ($shiftDetail && $shiftDetail->start_time && !$isOffDay) {
                $shiftStart = Carbon::parse($date . ' ' . $shiftDetail->start_time);
                $actualIn = Carbon::parse($date . ' ' . $clockIn);

                if ($actualIn->gt($shiftStart)) {
                    $lateMinutes = $actualIn->diffInMinutes($shiftStart);
                    if (!$hasExcusedBonus) {
                        $status = 'Late';
                    }
                }
            }

            $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
            if ($activeSchema) {
                if ($hasExcusedBonus) {
                    $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    $calculatedBonus = $maxTier ? $maxTier->nominal : 0.00;
                } else {
                    $matchingTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->where('max_late_minutes', '>=', $lateMinutes)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    $calculatedBonus = $matchingTier ? $matchingTier->nominal : 0.00;
                }
            }
        } elseif ($isOffDay) {
            // Priority 2: Holiday & Shift Day-Off (Employee has no clock-in on a scheduled day off / holiday)
            $status = 'Off';
            $calculatedBonus = 0.00;
        } elseif ($leave) {
            // Priority 3: Approved Leave on a scheduled working day
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
                    $calculatedBonus = $maxTier ? $maxTier->nominal : 0.00;
                }
            } else {
                $calculatedBonus = 0.00;
            }
        } else {
            // Priority 4: Working day with no clock-in and no approved leave
            if (!$status || $status === 'Present') {
                $status = 'Absent';
            }
            $calculatedBonus = 0.00;
        }

        return [
            'status' => $status ?? 'Present',
            'calculated_bonus' => $calculatedBonus,
        ];
    }
}
