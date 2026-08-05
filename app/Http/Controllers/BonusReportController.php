<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use App\Models\Employee;

class BonusReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $search = $request->query('search');
        $perPage = $request->query('per_page', 15);

        $schoolUnit = config('app.school_unit', 'smp');
        $unitStr = strtoupper($schoolUnit);
        $hrdUrl = Setting::get('hrd_api_url', env('HRD_URL', 'http://sans-hrd.test'));

        try {
            $response = Http::get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                'month' => $month,
                'unit_id' => strtolower($schoolUnit)
            ]);
            
            $json = $response->json();
            $reports = collect($json['data'] ?? []);
            
            // Extract from API response or calculate default
            $startDateReq = $json['start_date'] ?? null;
            $endDateReq = $json['end_date'] ?? null;
            $activeSchema = isset($json['active_schema']) ? (object) $json['active_schema'] : null;

            if (!$startDateReq || !$endDateReq) {
                $cutoffDate = (int) Setting::get('payroll_cutoff_date', 26);
                $monthCarbon = Carbon::createFromFormat('Y-m', $month);
                $endDateReq = $monthCarbon->copy()->setDay($cutoffDate)->format('Y-m-d');
                $startDateReq = $monthCarbon->copy()->subMonth()->setDay($cutoffDate + 1)->format('Y-m-d');
            }

            // Apply Role-based filtering
            $user = auth()->user();
            if ($user && $user->role === 'employee' && $user->employee_id) {
                // If it's a regular employee, only show their own report
                $reports = $reports->filter(function ($item) use ($user) {
                    return ($item['employee']['id'] ?? 0) == $user->employee_id;
                })->values();
            } else {
                // Apply Search filter for admins
                if (!empty($search)) {
                    $reports = $reports->filter(function ($item) use ($search) {
                        $name = $item['employee']['name'] ?? '';
                        $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                        return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                    })->values();
                }
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
            
            if ($user && $user->role === 'employee' && $user->employee_id) {
                return view('bonus-reports.employee-index', compact('paginatedReports', 'month', 'startDateReq', 'endDateReq', 'activeSchema', 'totalSemuaBonus'));
            }

            return view('bonus-reports.index', compact('paginatedReports', 'month', 'startDateReq', 'endDateReq', 'activeSchema', 'totalSemuaBonus'));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memuat rekap bonus dari HRD: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->with('error', 'Gagal memuat rekap bonus dari HRD: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $search = $request->query('search');
        $format = $request->query('format', 'excel');

        $schoolUnit = config('app.school_unit', 'smp');
        $unitStr = strtoupper($schoolUnit);
        $hrdUrl = Setting::get('hrd_api_url', env('HRD_URL', 'http://sans-hrd.test'));

        try {
            $response = Http::get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                'month' => $month,
                'unit_id' => strtolower($schoolUnit)
            ]);
            
            $json = $response->json();
            $reports = collect($json['data'] ?? []);
            
            $startDate = Carbon::parse($json['start_date'] ?? date('Y-m-d'));
            $endDate = Carbon::parse($json['end_date'] ?? date('Y-m-d'));
            $periodeStr = $startDate->format('d M') . ' - ' . $endDate->format('d M Y');

            $user = auth()->user();
            if ($user && $user->role === 'employee' && $user->employee_id) {
                $reports = $reports->filter(function ($item) use ($user) {
                    return ($item['employee']['id'] ?? 0) == $user->employee_id;
                })->values();
            } else {
                if (!empty($search)) {
                    $reports = $reports->filter(function ($item) use ($search) {
                        $name = $item['employee']['name'] ?? '';
                        $nip = $item['employee']['nuptk_nip_nik'] ?? '';
                        return stripos($name, $search) !== false || stripos($nip, $search) !== false;
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
                $sheet->setCellValue('D1', 'Total Bonus (Rp)');

                // Build dynamic date headers starting from column E (Index 5)
                $colIndex = 5;
                $hari = [0 => 'MIN', 1 => 'SEN', 2 => 'SEL', 3 => 'RAB', 4 => 'KAM', 5 => 'JUM', 6 => 'SAB'];
                foreach($dates as $date) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $dayName = $hari[$date->dayOfWeek];
                    $sheet->setCellValue($colLetter . '1', $dayName . "
" . $date->format('d/m'));
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
                        
                        if ($detail && $detail['status'] === 'Present') {
                            $sheet->setCellValue($colLetter . $row, $detail['bonus_nominal'] > 0 ? $detail['bonus_nominal'] : '-');
                        } else {
                            $sheet->setCellValue($colLetter . $row, '-');
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
                }, 'Rekap_Bonus_' . $unitStr . '_' . $month . '.xlsx', [
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
            
            return $pdf->download('Rekap_Bonus_' . $unitStr . '_' . $month . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat rekap bonus dari HRD: ' . $e->getMessage());
        }
    }
}

