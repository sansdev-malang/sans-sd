<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Employee;

class BonusReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        // Pegawai biasa diarahkan ke Data Kehadiran karena log absensi dan uang kehadirannya sudah ada di sana
        if ($user->role === 'employee') {
            return redirect()->route('attendances.index')->with('info', 'Rincian kehadiran dan uang kehadiran harian Anda dapat dilihat langsung pada menu Data Kehadiran.');
        }

        $cutoffDate = (int) Setting::get('payroll_cutoff_date', 26);
        $month = $request->query('month');
        if (empty($month)) {
            $today = now();
            $month = $today->day > $cutoffDate ? $today->copy()->startOfMonth()->addMonth()->format('Y-m') : $today->format('Y-m');
        }

        $isAdmin = $user->isAdmin();

        // Jika bukan Admin Unit / Super Admin, tampilkan kalender kehadiran & bonus personal
        if (! $isAdmin) {
            return $this->employeeCalendarIndex($request, $user, $month);
        }

        $search = $request->query('search');
        $perPage = $request->query('per_page', 50);

        $schoolUnit = config('app.school_unit', 'sd');
        $unitStr = strtoupper($schoolUnit);
        $hrdUrl = Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));

        try {
            $response = Http::withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                'school_unit_id' => config('app.school_unit_id', 2),
                'month' => $month,
                'unit_id' => strtolower($schoolUnit)
            ]);

            $json = $response->json();
            if (isset($json['cutoff_date'])) {
                Setting::set('payroll_cutoff_date', $json['cutoff_date']);
            }
            $reports = collect($json['data'] ?? []);

            // Extract from API response or calculate default
            $startDateReq = $json['start_date'] ?? null;
            $endDateReq = $json['end_date'] ?? null;
            $activeSchema = isset($json['active_schema']) ? (object) $json['active_schema'] : null;

            if (!$startDateReq || !$endDateReq) {
                $cutoffDate = (int) Setting::get('payroll_cutoff_date', 26);
                $monthCarbon = Carbon::parse($month . '-01');
                $endDateReq = $monthCarbon->copy()->setDay($cutoffDate)->format('Y-m-d');
                $startDateReq = $monthCarbon->copy()->subMonth()->setDay($cutoffDate + 1)->format('Y-m-d');
            }

            // Extract unique positions from local database for filtering
            $positions = \App\Models\Employee::whereNotNull('position')
                ->where('position', '!=', '')
                ->distinct()
                ->pluck('position')
                ->sort()
                ->values();

            // Preload employee positions for fast in-memory lookup
            $employeePositions = \App\Models\Employee::pluck('position', 'id')->toArray();

            // Apply Search filter for Super Admin
            if (!empty($search)) {
                $reports = $reports->filter(function ($item) use ($search) {
                    $name = $item['employee']['name'] ?? '';
                    $nip = $item['employee']['nuptk_nip_nik'] ?? ($item['employee']['nuptk'] ?? ($item['employee']['nip'] ?? ''));
                    return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                })->values();
            }

            // Apply Position filter for Super Admin
            $position = $request->query('position');
            if (!empty($position)) {
                $reports = $reports->filter(function ($item) use ($position, $employeePositions) {
                    $empId = $item['employee']['id'] ?? 0;
                    $empPos = $item['employee']['position'] ?? ($employeePositions[$empId] ?? '');
                    return $empPos === $position;
                })->values();
            }

            // Pagination
            $totalSemuaBonus = collect($reports)->sum('bonus_nominal');

            if ($perPage === 'all') {
                $paginatedReports = new LengthAwarePaginator($reports, $reports->count(), max(1, $reports->count()), 1, ['path' => $request->url(), 'query' => $request->query()]);
            } else {
                $perPage = (int) $perPage;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = $reports->slice(($currentPage - 1) * $perPage, $perPage)->values();
                $paginatedReports = new LengthAwarePaginator(
                    $currentItems,
                    $reports->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }

            return view('bonus-reports.index', compact('paginatedReports', 'month', 'startDateReq', 'endDateReq', 'activeSchema', 'totalSemuaBonus', 'positions'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memuat rekap bonus dari HRD: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->with('error', 'Gagal memuat rekap bonus dari HRD: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan kalender kehadiran & bonus personal untuk role non-super-admin (Kepala Sekolah, Admin Unit, Waka, dll.)
     */
    protected function employeeCalendarIndex(Request $request, $user, $month)
    {
        $empId = $user->employee_id ?? $user->employee?->id;
        if (! $empId) {
            $empId = Employee::where('email', $user->email)->orWhere('name', $user->name)->value('id');
        }

        $schoolUnit = config('app.school_unit', 'sd');
        $schoolUnitId = config('app.school_unit_id', 2);
        $forceRefresh = $request->boolean('refresh');
        $myActiveShifts = [];

        $monthCarbon = Carbon::parse($month . '-01');
        $isPastMonth = $monthCarbon->copy()->endOfMonth()->isPast();
        $previousMonth = $monthCarbon->copy()->subMonthNoOverflow()->format('Y-m');
        $isPrevPastMonth = Carbon::parse($previousMonth . '-01')->endOfMonth()->isPast();
        $nextMonth = $monthCarbon->copy()->addMonthNoOverflow()->format('Y-m');

        $cacheTtlPast = 86400;
        $matrixCacheKey = "hrd_att_matrix_{$schoolUnitId}_{$month}";
        $prevMatrixCacheKey = "hrd_att_matrix_{$schoolUnitId}_{$previousMonth}";
        $bonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$month}";
        $prevBonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$previousMonth}";
        $nextBonusCacheKey = "hrd_bonus_{$schoolUnitId}_{$nextMonth}";

        if ($forceRefresh) {
            Cache::forget($matrixCacheKey);
            Cache::forget($matrixCacheKey . '_stale');
            Cache::forget($prevMatrixCacheKey);
            Cache::forget($prevMatrixCacheKey . '_stale');
            Cache::forget($bonusCacheKey);
            Cache::forget($bonusCacheKey . '_stale');
            Cache::forget($prevBonusCacheKey);
            Cache::forget($prevBonusCacheKey . '_stale');
            Cache::forget($nextBonusCacheKey);
            Cache::forget($nextBonusCacheKey . '_stale');
        }

        $hrdUrl = Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));

        try {
            $matrixData = $isPastMonth ? Cache::get($matrixCacheKey) : null;
            $prevMatrixData = $isPrevPastMonth ? Cache::get($prevMatrixCacheKey) : null;
            $bonusData = $isPastMonth ? Cache::get($bonusCacheKey) : null;
            $prevBonusData = $isPrevPastMonth ? Cache::get($prevBonusCacheKey) : null;
            $nextBonusData = null;

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
            if (!$prevMatrixData) {
                $poolCalls['prev_matrix'] = function (\Illuminate\Http\Client\Pool $pool) use ($hrdUrl, $schoolUnitId, $schoolUnit, $previousMonth) {
                    return $pool->as('prev_matrix')->withHeaders(['X-API-TOKEN' => config('app.hrd_api_token')])->timeout(8.0)->get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                        'school_unit_id' => $schoolUnitId,
                        'unit_id' => strtolower($schoolUnit),
                        'month' => $previousMonth,
                        'start_date' => Carbon::parse($previousMonth . '-01')->startOfMonth()->format('Y-m-d'),
                        'end_date' => Carbon::parse($previousMonth . '-01')->endOfMonth()->format('Y-m-d'),
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

            if (!empty($poolCalls)) {
                $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($poolCalls) {
                    $callList = [];
                    foreach ($poolCalls as $key => $call) {
                        $callList[$key] = $call($pool);
                    }
                    return $callList;
                });

                if (isset($responses['curr_matrix']) && $responses['curr_matrix'] instanceof \Illuminate\Http\Client\Response && $responses['curr_matrix']->successful()) {
                    $matrixData = $responses['curr_matrix']->json();
                    if ($isPastMonth) {
                        Cache::put($matrixCacheKey, $matrixData, $cacheTtlPast);
                    }
                    Cache::put($matrixCacheKey . '_stale', $matrixData, 604800);
                }
                if (isset($responses['prev_matrix']) && $responses['prev_matrix'] instanceof \Illuminate\Http\Client\Response && $responses['prev_matrix']->successful()) {
                    $prevMatrixData = $responses['prev_matrix']->json();
                    if ($isPrevPastMonth) {
                        Cache::put($prevMatrixCacheKey, $prevMatrixData, $cacheTtlPast);
                    }
                    Cache::put($prevMatrixCacheKey . '_stale', $prevMatrixData, 604800);
                }
                if (isset($responses['curr_bonus']) && $responses['curr_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['curr_bonus']->successful()) {
                    $bonusData = $responses['curr_bonus']->json();
                    if ($isPastMonth) {
                        Cache::put($bonusCacheKey, $bonusData, $cacheTtlPast);
                    }
                    Cache::put($bonusCacheKey . '_stale', $bonusData, 604800);
                }
                if (isset($responses['prev_bonus']) && $responses['prev_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['prev_bonus']->successful()) {
                    $prevBonusData = $responses['prev_bonus']->json();
                    if ($isPrevPastMonth) {
                        Cache::put($prevBonusCacheKey, $prevBonusData, $cacheTtlPast);
                    }
                    Cache::put($prevBonusCacheKey . '_stale', $prevBonusData, 604800);
                }
                if (isset($responses['next_bonus']) && $responses['next_bonus'] instanceof \Illuminate\Http\Client\Response && $responses['next_bonus']->successful()) {
                    $nextBonusData = $responses['next_bonus']->json();
                    Cache::put($nextBonusKey = $nextBonusCacheKey . '_stale', $nextBonusData, 604800);
                }
            }

            if (!$matrixData) $matrixData = Cache::get($matrixCacheKey . '_stale');
            if (!$prevMatrixData) $prevMatrixData = Cache::get($prevMatrixCacheKey . '_stale');
            if (!$bonusData) $bonusData = Cache::get($bonusCacheKey . '_stale');
            if (!$prevBonusData) $prevBonusData = Cache::get($prevBonusCacheKey . '_stale');
            if (!$nextBonusData) $nextBonusData = Cache::get($nextBonusCacheKey . '_stale');

            if (isset($matrixData['cutoff_date'])) {
                Setting::set('payroll_cutoff_date', $matrixData['cutoff_date']);
            }
            $reports = collect($matrixData['data'] ?? []);
            $startDate = Carbon::parse($matrixData['start_date'] ?? ($month . '-01'));
            $endDate = Carbon::parse($matrixData['end_date'] ?? $monthCarbon->copy()->endOfMonth()->format('Y-m-d'));

            $previousReports = collect($prevMatrixData['data'] ?? []);
            $previousReport = $previousReports->first(function ($item) use ($empId) {
                return ($item['employee']['id'] ?? 0) == $empId;
            });

            $currentReport = $reports->first(function ($item) use ($empId) {
                return ($item['employee']['id'] ?? 0) == $empId;
            });

            if ($currentReport && $previousReport) {
                $currentDetails = $currentReport['daily_details'] ?? [];
                $previousDetails = $previousReport['daily_details'] ?? [];
                $currentReport['daily_details'] = array_replace($previousDetails, $currentDetails);
                $reports = collect([$currentReport]);
            } elseif ($currentReport) {
                $reports = collect([$currentReport]);
            } elseif ($previousReport) {
                $reports = collect([$previousReport]);
            } else {
                $bonusReports = collect($bonusData['data'] ?? []);
                $currentBonus = $bonusReports->first(function ($br) use ($empId) {
                    return ($br['employee']['id'] ?? 0) == $empId;
                });
                if ($currentBonus) {
                    $reports = collect([$currentBonus]);
                } else {
                    $reports = collect();
                }
            }

            $bonusReports = collect($bonusData['data'] ?? []);
            $previousBonusReports = collect($prevBonusData['data'] ?? []);
            $nextBonusReports = collect($nextBonusData['data'] ?? []);

            $currentBonus = $bonusReports->first(function ($br) use ($empId) {
                return ($br['employee']['id'] ?? 0) == $empId;
            });
            if ($currentBonus && isset($currentBonus['active_shifts'])) {
                $myActiveShifts = $currentBonus['active_shifts'];
            }

            $reports = $reports->map(function ($report) use ($bonusReports, $previousBonusReports, $nextBonusReports, $empId) {
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
                    $bonusDetails = array_replace($bonusDetails, $currentBonus['daily_details']);
                }
                if ($nextBonus && isset($nextBonus['daily_details'])) {
                    $bonusDetails = array_replace($bonusDetails, $nextBonus['daily_details']);
                }

                if (isset($report['daily_details'])) {
                    $details = $report['daily_details'];
                    foreach ($details as $dateStr => &$detail) {
                        $bonusDetail = $bonusDetails[$dateStr] ?? null;
                        $detail['calculated_bonus'] = $bonusDetail ? (float)($bonusDetail['bonus_nominal'] ?? 0.00) : (float)($detail['bonus_nominal'] ?? 0.00);
                        if (empty($detail['check_in']) && !empty($bonusDetail['check_in'])) {
                            $detail['check_in'] = $bonusDetail['check_in'];
                        }
                        if (!isset($detail['is_late']) && isset($bonusDetail['late_minutes'])) {
                            $detail['is_late'] = ($bonusDetail['late_minutes'] > 0);
                        }
                    }
                    $report['daily_details'] = $details;
                }
                return $report;
            });

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal memuat matriks kalender bonus: ' . $e->getMessage());
            $startDate = $monthCarbon->copy()->startOfMonth();
            $endDate = $monthCarbon->copy()->endOfMonth();
            $reports = collect();
        }

        return view('bonus-reports.employee-index', compact('reports', 'month', 'startDate', 'endDate', 'myActiveShifts'));
    }

    public function export(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $search = $request->query('search');
        $format = $request->query('format', 'excel');

        $schoolUnit = config('app.school_unit', 'sd');
        $unitStr = strtoupper($schoolUnit);
        $hrdUrl = Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));

        try {
            $response = Http::withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                'school_unit_id' => config('app.school_unit_id', 2),
                'month' => $month
            ]);

            $json = $response->json();
            $reports = collect($json['data'] ?? []);

            $startDate = Carbon::parse($json['start_date'] ?? date('Y-m-d'));
            $endDate = Carbon::parse($json['end_date'] ?? date('Y-m-d'));
            $periodeStr = $startDate->format('d M') . ' - ' . $endDate->format('d M Y');

            $user = auth()->user();
            if (! $user) {
                abort(403);
            }

            if ($user->role === 'employee') {
                return redirect()->route('attendances.index');
            }

            $isAdmin = $user->isAdmin();

            if (! $isAdmin) {
                $empId = $user->employee_id ?? $user->employee?->id;
                if (! $empId) {
                    $empId = Employee::where('email', $user->email)->orWhere('name', $user->name)->value('id');
                }
                if ($empId) {
                    $reports = $reports->filter(function ($item) use ($empId) {
                        return ($item['employee']['id'] ?? 0) == $empId;
                    })->values();
                } else {
                    $reports = collect();
                }
            } else {
                if (!empty($search)) {
                    $reports = $reports->filter(function ($item) use ($search) {
                        $name = $item['employee']['name'] ?? '';
                        $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                        return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                    })->values();
                }

                // Apply Position filter for export
                $position = $request->query('position');
                if (!empty($position)) {
                    $reports = $reports->filter(function ($item) use ($position) {
                        $empId = $item['employee']['id'] ?? 0;
                        $emp = \App\Models\Employee::find($empId);
                        return $emp && $emp->position === $position;
                    })->values();
                }
            }

            $dates = [];
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dates[] = clone $currentDate;
                $currentDate->addDay();
            }

            $reportsArr = $reports->toArray();
            $totalSemuaBonus = collect($reportsArr)->sum('bonus_nominal');

            if ($format === 'excel') {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Nama Pegawai');
                $sheet->setCellValue('C1', 'Unit');
                $sheet->setCellValue('D1', 'Total Uang Kehadiran (Rp)');

                // Build dynamic date headers starting from column E (Index 5)
                $colIndex = 5;
                $hari = [0 => 'MIN', 1 => 'SEN', 2 => 'SEL', 3 => 'RAB', 4 => 'KAM', 5 => 'JUM', 6 => 'SAB'];
                foreach($dates as $date) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $dayName = $hari[$date->dayOfWeek];
                    $sheet->setCellValue($colLetter . '1', $dayName . "\n" . $date->format('d/m'));
                    if ($date->dayOfWeek == 0) {
                        $sheet->getStyle($colLetter . '1')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                    }
                    $colIndex++;
                }

                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex - 1);

                $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . $lastColLetter . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEFEFEF');
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getStyle('A1:' . $lastColLetter . '1')->getAlignment()->setWrapText(true)
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $row = 2;
                $no = 1;
                foreach ($reportsArr as $report) {
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $report['employee']['name']);
                    $sheet->setCellValue('C' . $row, $report['employee']['unit']['name'] ?? ($report['employee']['unit_name'] ?? '-'));
                    $sheet->setCellValue('D' . $row, $report['bonus_nominal']);

                    $colIdx = 5;
                    foreach($dates as $date) {
                        $dateStr = $date->format('Y-m-d');
                        $detail = $report['daily_details'][$dateStr] ?? null;
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);

                        if ($detail) {
                            if (isset($detail['bonus_nominal']) && $detail['bonus_nominal'] > 0) {
                                $sheet->setCellValue($colLetter . $row, $detail['bonus_nominal']);
                            } else {
                                $status = $detail['status'] ?? '';
                                if (in_array($status, ['Off', 'Libur'])) {
                                    $sheet->setCellValue($colLetter . $row, 'OFF');
                                } else {
                                    $sheet->setCellValue($colLetter . $row, '0k');
                                    $sheet->getStyle($colLetter . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED));
                                }
                            }
                        } else {
                            $sheet->setCellValue($colLetter . $row, 'OFF');
                        }

                        // Align center
                        $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $colIdx++;
                    }
                    $row++;
                }

                $sheet->setCellValue('C' . $row, 'TOTAL:');
                $sheet->getStyle('C' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('D' . $row, $totalSemuaBonus);
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);

                $sheet->getStyle('D2:D'.$row)->getNumberFormat()->setFormatCode('#,##0');

                foreach(range(1, $colIndex - 1) as $c) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }

                $sheet->freezePane('E2');

                return response()->streamDownload(function() use ($spreadsheet) {
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                }, 'Rekap_Uang_Kehadiran_' . $unitStr . '_' . $month . '.xlsx', [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0'
                ]);
            }

            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', '300');

            // PDF Export
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bonus-reports.export-pdf', [
                'reports' => $reportsArr,
                'periodeStr' => $periodeStr,
                'dates' => $dates,
                'totalSemuaBonus' => $totalSemuaBonus
            ])->setPaper('a4', 'landscape');

            return $pdf->download('Rekap_Uang_Kehadiran_' . $unitStr . '_' . $month . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat rekap bonus dari HRD: ' . $e->getMessage());
        }
    }
}
