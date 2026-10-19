<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\Employee;

class AttendancePercentageReportController extends Controller
{
    /**
     * Tampilkan Laporan Persentase Kehadiran untuk Unit SD.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        $isAdmin = $user->isAdmin();

        // Tentukan batas tanggal cutoff payroll
        $cutoffDate = (int) Setting::get('payroll_cutoff_date', 26);
        $month = $request->query('month');
        if (empty($month)) {
            $today = now();
            $month = $today->day > $cutoffDate ? $today->copy()->startOfMonth()->addMonth()->format('Y-m') : $today->format('Y-m');
        }

        $schoolUnit = config('app.school_unit', 'sd');
        $schoolUnitId = config('app.school_unit_id', 2);
        $hrdUrl = Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
        $forceRefresh = $request->boolean('refresh');

        $cacheKey = "hrd_att_percentage_{$schoolUnitId}_{$month}";
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        $monthCarbon = Carbon::parse($month . '-01');
        $isPastMonth = $monthCarbon->copy()->endOfMonth()->isPast();
        $cacheTtl = $isPastMonth ? 86400 : 300;

        try {
            $data = Cache::remember($cacheKey, $cacheTtl, function () use ($hrdUrl, $schoolUnit, $schoolUnitId, $month, $request) {
                $response = Http::withHeaders([
                    'X-API-TOKEN' => config('app.hrd_api_token')
                ])->timeout(30)->get(rtrim($hrdUrl, '/') . '/api/attendance-percentage-reports', [
                    'school_unit_id' => $schoolUnitId,
                    'unit_id' => strtolower($schoolUnit),
                    'month' => $month,
                    'start_date' => $request->query('start_date'),
                    'end_date' => $request->query('end_date'),
                ]);

                if (! $response->successful()) {
                    throw new \Exception('Gagal menghubungi API HRD: ' . ($response->json('message') ?? $response->body()));
                }

                return $response->json();
            });

            if (isset($data['cutoff_date'])) {
                Setting::set('payroll_cutoff_date', $data['cutoff_date']);
            }

            $startDateReq = $data['start_date'] ?? null;
            $endDateReq = $data['end_date'] ?? null;
            $summary = $data['summary'] ?? [];
            $apiPositions = collect($data['positions'] ?? []);
            $rawReports = collect($data['reports'] ?? []);

            // Strictly filter out Tukang from reports and all calculations
            $tukangEmployeeIds = Employee::where(function($q) {
                $q->whereHas('employeeType', function($subQ) {
                    $subQ->where('code', 'tukang')->orWhere('name', 'like', '%tukang%');
                })->orWhere('position', 'like', '%tukang%');
            })->pluck('id')->toArray();

            $rawReports = $rawReports->reject(function ($item) use ($tukangEmployeeIds) {
                $pos = strtolower(trim((string)($item['employee']['position'] ?? $item['employee']['subject_position'] ?? '')));
                $type = strtolower(trim((string)($item['employee']['employee_type']['name'] ?? $item['employee']['employee_type']['code'] ?? (is_string($item['employee']['employee_type'] ?? null) ? $item['employee']['employee_type'] : ''))));
                $code = strtolower(trim((string)($item['employee']['employee_type']['code'] ?? '')));
                $name = strtolower(trim((string)($item['employee']['name'] ?? '')));
                $id = $item['employee']['id'] ?? null;

                return in_array($id, $tukangEmployeeIds) 
                    || str_contains($pos, 'tukang') 
                    || str_contains($type, 'tukang') 
                    || $type === 'tkg' 
                    || $code === 'tkg' 
                    || str_contains($name, 'tukang');
            })->values();

            // Ambil data jabatan lokal untuk melengkapi opsi filter (kecuali Tukang)
            $localPositions = Employee::whereNotIn('id', $tukangEmployeeIds)
                ->whereNotNull('position')
                ->where('position', '!=', '')
                ->whereRaw("LOWER(position) NOT LIKE '%tukang%'")
                ->distinct()
                ->pluck('position');

            $positions = $apiPositions->merge($localPositions)
                ->filter(fn($p) => !str_contains(strtolower((string)$p), 'tukang'))
                ->unique()
                ->sort()
                ->values();

            // Jika user adalah Pegawai biasa (bukan admin), batasi hanya melihat datanya sendiri
            if (! $isAdmin) {
                $empId = $user->employee_id ?? $user->employee?->id;
                $rawReports = $rawReports->filter(function ($item) use ($empId, $user) {
                    if ($empId && ($item['employee']['id'] ?? null) == $empId) {
                        return true;
                    }
                    $name = $item['employee']['name'] ?? '';
                    return stripos($name, $user->name) !== false;
                })->values();
            }

            // Filter Pencarian Nama / NIP
            $search = $request->query('search');
            if (!empty($search)) {
                $rawReports = $rawReports->filter(function ($item) use ($search) {
                    $name = $item['employee']['name'] ?? '';
                    $nip = $item['employee']['nip'] ?? ($item['employee']['nuptk_nip_nik'] ?? '');
                    return stripos($name, $search) !== false || stripos($nip, $search) !== false;
                })->values();
            }

            // Filter Jabatan (Multi-Select)
            $selectedPositions = (array) $request->query('positions', []);
            if ($request->filled('position')) {
                $selectedPositions[] = $request->query('position');
            }
            $selectedPositions = array_filter(array_unique($selectedPositions));

            if (!empty($selectedPositions)) {
                $rawReports = $rawReports->filter(function ($item) use ($selectedPositions) {
                    $pos = $item['employee']['position'] ?? ($item['employee']['subject_position'] ?? '');
                    return in_array($pos, $selectedPositions);
                })->values();
            }

            $reports = $rawReports->values();

            // Hitung ringkasan KPI untuk strip kategori & ringkasan unit
            $countTotal = $reports->count();
            $countGreen = $reports->filter(fn($r) => ($r['percentage'] ?? 0) >= 95)->count();
            $countYellow = $reports->filter(fn($r) => ($r['percentage'] ?? 0) >= 90 && ($r['percentage'] ?? 0) < 95)->count();
            $countRed = $reports->filter(fn($r) => ($r['percentage'] ?? 0) < 90)->count();

            // Hitung persentase kehadiran keseluruhan unit (dinamis & bebas Tukang)
            $unitAveragePercentage = $countTotal > 0 ? round($reports->avg('percentage'), 1) : 0;
            $summary['unit_average_percentage'] = $unitAveragePercentage;
            $summary['average_percentage'] = $unitAveragePercentage;
            $summary['total_employees'] = $countTotal;
            $summary['total_present'] = $reports->sum('total_present');
            $summary['total_absent'] = $reports->sum('total_absent');
            $summary['total_izin'] = $reports->sum('total_izin');
            $summary['total_sakit'] = $reports->sum('total_sakit');
            $summary['total_cuti'] = $reports->sum('total_cuti');

            // Hitung Statistik Kehadiran per Jabatan
            $positionStats = $reports->groupBy(function($r) {
                return trim($r['employee']['position'] ?? $r['employee']['subject_position'] ?? 'Lainnya') ?: 'Lainnya';
            })->map(function($group, $posName) {
                $totalStaff = $group->count();
                $avgPercentage = round($group->avg('percentage'), 1);
                $totalHadir = $group->sum('total_present');
                $totalAlpa = $group->sum('total_absent');
                $totalIzin = $group->sum('total_izin');
                $totalSakit = $group->sum('total_sakit');
                $totalCuti = $group->sum('total_cuti');
                $totalLateCount = $group->sum('late_count');
                $totalLateMinutes = $group->sum('total_late_minutes');

                return [
                    'position' => $posName,
                    'staff_count' => $totalStaff,
                    'avg_percentage' => $avgPercentage,
                    'total_present' => $totalHadir,
                    'total_absent' => $totalAlpa,
                    'total_izin' => $totalIzin,
                    'total_sakit' => $totalSakit,
                    'total_cuti' => $totalCuti,
                    'total_late_count' => $totalLateCount,
                    'total_late_minutes' => $totalLateMinutes,
                ];
            })->sortByDesc('avg_percentage')->values();

            // Hitung Analisis Keterlambatan yang jelas dan informatif
            $onTimeStaffCount = $reports->filter(fn($r) => ($r['late_count'] ?? 0) === 0)->count();
            $lateStaffCount = $reports->filter(fn($r) => ($r['late_count'] ?? 0) > 0)->count();
            $onTimePercentage = $countTotal > 0 ? round(($onTimeStaffCount / $countTotal) * 100, 1) : 100;

            $dur1to15 = 0;   // 1 - 15 menit (Ringan)
            $dur16to30 = 0;  // 16 - 30 menit (Sedang)
            $durOver30 = 0;  // > 30 menit (Berat)

            foreach ($reports as $r) {
                if (!empty($r['day_details'])) {
                    foreach ($r['day_details'] as $d) {
                        $lm = (int) ($d['late_minutes'] ?? 0);
                        if ($lm > 0) {
                            if ($lm <= 15) {
                                $dur1to15++;
                            } elseif ($lm <= 30) {
                                $dur16to30++;
                            } else {
                                $durOver30++;
                            }
                        }
                    }
                }
            }

            $totalLateEvents = $dur1to15 + $dur16to30 + $durOver30;
            if ($totalLateEvents === 0 && isset($summary['total_late_events'])) {
                $totalLateEvents = (int) $summary['total_late_events'];
            }
            $totalLateMinutes = $reports->sum('total_late_minutes');
            if ($totalLateMinutes === 0 && isset($summary['total_late_minutes'])) {
                $totalLateMinutes = (int) $summary['total_late_minutes'];
            }
            $avgLateMinutes = $totalLateEvents > 0 ? round($totalLateMinutes / $totalLateEvents) : 0;

            $lateAnalysis = [
                'on_time_staff_count' => $onTimeStaffCount,
                'late_staff_count' => $lateStaffCount,
                'on_time_percentage' => $onTimePercentage,
                'dur_1_to_15' => $dur1to15,
                'dur_16_to_30' => $dur16to30,
                'dur_over_30' => $durOver30,
                'total_late_events' => $totalLateEvents,
                'total_late_minutes' => $totalLateMinutes,
                'avg_late_minutes' => $avgLateMinutes,
            ];

            // Navigasi Bulan Lalu & Bulan Depan
            $prevMonth = $monthCarbon->copy()->subMonthNoOverflow()->format('Y-m');
            $nextMonth = $monthCarbon->copy()->addMonthNoOverflow()->format('Y-m');

            return view('attendance-percentage-reports.index', compact(
                'reports',
                'summary',
                'unitAveragePercentage',
                'positions',
                'selectedPositions',
                'positionStats',
                'lateAnalysis',
                'month',
                'prevMonth',
                'nextMonth',
                'startDateReq',
                'endDateReq',
                'countTotal',
                'countGreen',
                'countYellow',
                'countRed',
                'schoolUnit',
                'isAdmin'
            ));

        } catch (\Exception $e) {
            Log::error('Gagal memuat persentase kehadiran dari HRD: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return back()->with('error', 'Gagal memuat data persentase kehadiran dari HRD: ' . $e->getMessage());
        }
    }
}
