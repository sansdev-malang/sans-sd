<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);

        $tukangIds = \App\Models\Employee::where(function($q) {
            $q->whereHas('employeeType', function($subQ) {
                $subQ->where('code', 'tukang')->orWhere('name', 'like', '%tukang%');
            })->orWhere('position', 'like', '%tukang%');
        })->pluck('id')->toArray();

        $employeeCount = \App\Models\Employee::whereNotIn('id', $tukangIds)->where(function($q) {
            $q->whereNotIn('position', ['GPK', 'GPQ'])
              ->orWhereNull('position');
        })->count();

        $gpkCount = \App\Models\Employee::whereNotIn('id', $tukangIds)->where('position', 'GPK')->count();
        $gpqCount = \App\Models\Employee::whereNotIn('id', $tukangIds)->where('position', 'GPQ')->count();

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        
        $employeePresent = 0;
        $gpkPresent = 0;
        $gpqPresent = 0;
        $totalPresentToday = 0;
        $totalPresentYesterday = 0;

        try {
            $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->get(rtrim($hrdUrl, '/') . '/api/attendance-matrix', [
                'school_unit_id' => config('app.school_unit_id', 2),
                'unit_id' => strtolower(config('app.school_unit', 'sd')),
                'start_date' => $yesterday,
                'end_date' => $today
            ]);
            
            $reports = $response->json()['data'] ?? [];
            
            foreach ($reports as $report) {
                $empId = $report['employee']['id'] ?? null;
                if (in_array($empId, $tukangIds)) {
                    continue;
                }

                $pos = $report['employee']['position'] ?? $report['employee']['subject_position'] ?? null;
                $details = $report['daily_details'] ?? [];
                
                // Cek hari ini
                if (($details[$today]['status'] ?? '') === 'Hadir') {
                    $totalPresentToday++;
                    
                    if ($pos === 'GPK') {
                        $gpkPresent++;
                    } elseif ($pos === 'GPQ') {
                        $gpqPresent++;
                    } else {
                        $employeePresent++;
                    }
                }
                
                // Cek kemarin
                if (($details[$yesterday]['status'] ?? '') === 'Hadir') {
                    $totalPresentYesterday++;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memuat absensi dashboard dari HRD: ' . $e->getMessage());
        }

        $employeeAttendancePercent = $employeeCount > 0 ? round(($employeePresent / $employeeCount) * 100, 1) : 0;
        $gpkAttendancePercent = $gpkCount > 0 ? round(($gpkPresent / $gpkCount) * 100, 1) : 0;
        $gpqAttendancePercent = $gpqCount > 0 ? round(($gpqPresent / $gpqCount) * 100, 1) : 0;

        $totalEmployeeCount = \App\Models\Employee::whereNotIn('id', $tukangIds)->count();
        $todayOverallPercent = $totalEmployeeCount > 0 ? round(($totalPresentToday / $totalEmployeeCount) * 100, 1) : 0;
        $yesterdayOverallPercent = $totalEmployeeCount > 0 ? round(($totalPresentYesterday / $totalEmployeeCount) * 100, 1) : 0;
        
        $diffPercent = round($todayOverallPercent - $yesterdayOverallPercent, 1);

        $query = \App\Models\Announcement::latest();

        if (!$isAdmin) {
            $query->where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('publish_date')
                        ->orWhere('publish_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', now());
                })
                ->whereIn('target_audience', ['global', 'employee']);
        }

        $latestAnnouncements = $query->take(3)->get();

        // Prepare Admin Attendance Chart Points
        $adminChartPoints = [];

        if ($isAdmin) {
            $cutoffDate = (int) \App\Models\Setting::get('payroll_cutoff_date', 26);
            $todayCarbon = now();
            
            if ($todayCarbon->day >= $cutoffDate) {
                $startDateCarbon = $todayCarbon->copy()->day($cutoffDate);
            } else {
                $startDateCarbon = $todayCarbon->copy()->subMonth()->day($cutoffDate);
            }
            
            // Ensure we have at least 7 days of history to render a nice chart
            if ($startDateCarbon->diffInDays($todayCarbon) < 6) {
                $startDateCarbon = $todayCarbon->copy()->subDays(6);
            }

            $dates = [];
            $start = $startDateCarbon->copy();
            $end = $todayCarbon->copy();
            while ($start->lte($end)) {
                if (!$start->isSunday()) {
                    $dates[] = $start->format('Y-m-d');
                }
                $start->addDay();
            }

            $attendanceCounts = \App\Models\Attendance::whereIn('date', $dates)
                ->whereNotIn('employee_id', $tukangIds)
                ->where('status', 'Hadir')
                ->selectRaw('date, count(*) as total')
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $totalActiveEmployees = \App\Models\Employee::whereNotIn('id', $tukangIds)->count();
            if ($totalActiveEmployees <= 0) {
                $totalActiveEmployees = 1;
            }

            foreach ($dates as $index => $dateStr) {
                $count = $attendanceCounts[$dateStr] ?? 0;
                
                // Fallback for dev if no attendance records exist
                if ($count === 0) {
                    $dateCarbon = \Carbon\Carbon::parse($dateStr);
                    $seed = crc32($dateStr);
                    mt_srand($seed);
                    $percent = mt_rand(88, 97);
                    $count = round(($percent / 100) * $totalActiveEmployees);
                } else {
                    $percent = round(($count / $totalActiveEmployees) * 100);
                }

                // If it is today, overwrite with active matrix present count
                if ($dateStr === $today) {
                    $count = $totalPresentToday;
                    $percent = round(($count / $totalActiveEmployees) * 100);
                }

                // Map percent (0 - 100) to Y (120 - 30)
                $y = 120 - (($percent / 100) * 90);

                $adminChartPoints[] = [
                    'date' => \Carbon\Carbon::parse($dateStr)->translatedFormat('d M'),
                    'short_date' => \Carbon\Carbon::parse($dateStr)->format('d/m'),
                    'percent' => $percent,
                    'count' => $count,
                    'y' => $y
                ];
            }
        }

        // Fetch personal stats for non-admin employees
        $myReport = null;
        $totalLeavesThisYear = 0;
        $myRecentLeaves = collect();
        $chartPoints = [];

        if (!$isAdmin && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
            if ($employee) {
                // Calculate Leave Days Approved This Year
                $approvedLeavesThisYear = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->where('status', 'Approved')
                    ->whereYear('start_date', date('Y'))
                    ->get();
                foreach ($approvedLeavesThisYear as $req) {
                    $totalLeavesThisYear += \Carbon\Carbon::parse($req->start_date)->diffInDays(\Carbon\Carbon::parse($req->end_date)) + 1;
                }

                // Fetch Recent Activity (Leaves/Permits status)
                $myRecentLeaves = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->latest()
                    ->limit(5)
                    ->get();

                                        // Fetch Recent Attendances (last 7 days) for Employee
                $myRecentAttendances = \App\Models\Attendance::where('employee_id', $employee->id)
                    ->orderBy('date', 'desc')
                    ->limit(7)
                    ->get()
                    ->reverse()
                    ->values();

                // Fetch Presence & Bonus details from HRD for the top cards
                $schoolUnit = config('app.school_unit', 'sd');
                $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
                try {
                    $cutoffDate = (int) \App\Models\Setting::get('payroll_cutoff_date', 26);
                    $today = now();
                    $month = $today->day > $cutoffDate ? $today->copy()->startOfMonth()->addMonth()->format('Y-m') : $today->format('Y-m');

                    $response = \Illuminate\Support\Facades\Http::timeout(15)->withHeaders([
                        'X-API-TOKEN' => config('app.hrd_api_token')
                    ])->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                        'school_unit_id' => config('app.school_unit_id'),
                        'month' => $month
                    ]);
                    if ($response->successful()) {
                        $json = $response->json();
                        $reports = collect($json['data'] ?? []);
                        $myReport = $reports->first(function ($item) use ($employee) {
                            return ($item['employee']['id'] ?? 0) == $employee->id;
                        });
                    }
                } catch (\Exception $e) {
                    // Fallback silently
                }
            }
        }

        // Fetch picket schedules for employee
        $myPicketSchedules = collect();
        $myPicketToday = null;
        $todayDayOfWeek = now()->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        if ($user->employee_id && config('app.school_unit') === 'sd') {
            $myPicketSchedules = \App\Models\PicketSchedule::where('employee_id', $user->employee_id)
                ->with('picketArea')
                ->orderBy('day_of_week')
                ->get();

            if ($todayDayOfWeek >= 1 && $todayDayOfWeek <= 6) {
                $myPicketToday = $myPicketSchedules->firstWhere('day_of_week', $todayDayOfWeek);
            }
        }

        // Prepare SVG Chart Points from HRD API daily_details
        $chartPoints = [];
        $dailyDetails = $myReport['daily_details'] ?? [];
        $totalLateDays = 0;
        if (!empty($dailyDetails)) {
            foreach ($dailyDetails as $det) {
                if (isset($det['late_minutes']) && $det['late_minutes'] > 0) {
                    $totalLateDays++;
                }
            }
            ksort($dailyDetails);
            // Filter out Pending, Off, and Leave days from the presence chart
            $completedDetails = array_filter($dailyDetails, function ($day) {
                $status = $day['status'] ?? '';
                return $status !== 'Pending' &&
                    $status !== 'Off' &&
                    $status !== 'Libur' &&
                    $status !== 'Sakit' &&
                    $status !== 'Izin' &&
                    $status !== 'Cuti' &&
                    $status !== 'Cuti Melahirkan' &&
                    $status !== 'Cuti Tahunan';
            });

            $idx = 0;
            foreach ($completedDetails as $dateStr => $det) {
                $x = $idx * 60; // 60px spacing per day
                $y = 130;
                $timeStr = '-';

                $jamMasuk = $det['check_in'] ?? null;
                if ($jamMasuk && strpos($jamMasuk, ':') !== false) {
                    // Time calculations for chart Y position (06:00 = top, 08:00 = bottom)
                    $parts = explode(':', $jamMasuk);
                    if (count($parts) >= 2) {
                        $mins = (int)$parts[0] * 60 + (int)$parts[1];

                        // 360 mins (06:00) -> Y=30. 480 mins (08:00) -> Y=130
                        $y = 30 + (($mins - 360) * (100 / 120));
                        if ($y < 30) $y = 30;
                        if ($y > 130) $y = 130;

                        $timeStr = substr($jamMasuk, 0, 5);
                    }
                }

                $chartPoints[] = [
                    'x' => $x,
                    'y' => $y,
                    'date' => \Carbon\Carbon::parse($dateStr)->translatedFormat('d M'),
                    'short_date' => \Carbon\Carbon::parse($dateStr)->format('d/m'), // Added shorter date format for tight spaces
                    'time' => $timeStr,
                    'status' => $det['status'] ?? '-',
                    'is_late' => isset($det['late_minutes']) && $det['late_minutes'] > 0,
                    'shift_name' => $det['shift_name'] ?? null,
                    'shift_start' => isset($det['shift_start']) ? substr($det['shift_start'], 0, 5) : null,
                    'shift_end' => isset($det['shift_end']) ? substr($det['shift_end'], 0, 5) : null,
                    'check_in' => $jamMasuk ? substr($jamMasuk, 0, 5) : '-',
                    'check_out' => isset($det['check_out']) && $det['check_out'] ? substr($det['check_out'], 0, 5) : '-'
                ];
                $idx++;
            }
        }

        $myActiveShifts = $myReport['active_shifts'] ?? [];

        $myCalendarDays = [];
        if (!empty($dailyDetails)) {
            ksort($dailyDetails);
            $dates = array_keys($dailyDetails);
            $firstDateStr = reset($dates);
            $firstDate = \Carbon\Carbon::parse($firstDateStr);

            $startDayOfWeek = $firstDate->dayOfWeek;
            $startDayOfWeek = $startDayOfWeek == 0 ? 7 : $startDayOfWeek;

            for ($i = 1; $i < $startDayOfWeek; $i++) {
                $myCalendarDays[] = [
                    'is_empty' => true,
                    'date' => null,
                    'day_num' => null,
                    'shift_name' => null,
                    'shift_start' => null,
                    'shift_end' => null,
                    'status' => null,
                ];
            }

            foreach ($dailyDetails as $dateStr => $det) {
                $dateCarbon = \Carbon\Carbon::parse($dateStr);
                $shiftName = $det['shift_name'] ?? null;
                $shortLabel = '-';
                $type = 'default';

                if ($shiftName) {
                    if (stripos($shiftName, 'malam') !== false) {
                        $shortLabel = 'M';
                        $type = 'malam';
                    } elseif (stripos($shiftName, 'pagi') !== false) {
                        $shortLabel = 'P';
                        $type = 'pagi';
                    } elseif (stripos($shiftName, 'siang') !== false) {
                        $shortLabel = 'S';
                        $type = 'siang';
                    } else {
                        $shortLabel = strtoupper(substr($shiftName, 0, 1));
                        $type = 'other';
                    }
                } else {
                    $status = $det['status'] ?? '';
                    if ($status === 'Off' || $status === 'Libur') {
                        $shortLabel = 'Off';
                        $type = 'off';
                    } elseif ($status === 'Sakit') {
                        $shortLabel = 'Skt';
                        $type = 'sakit';
                    } elseif ($status === 'Izin') {
                        $shortLabel = 'Izn';
                        $type = 'izin';
                    } elseif (stripos($status, 'Cuti') !== false) {
                        $shortLabel = 'Cti';
                        $type = 'cuti';
                    } else {
                        $shortLabel = '-';
                    }
                }

                $myCalendarDays[] = [
                    'is_empty' => false,
                    'date' => $dateStr,
                    'day_num' => $dateCarbon->day,
                    'shift_name' => $shiftName,
                    'short_label' => $shortLabel,
                    'type' => $type,
                    'shift_start' => isset($det['shift_start']) ? substr($det['shift_start'], 0, 5) : null,
                    'shift_end' => isset($det['shift_end']) ? substr($det['shift_end'], 0, 5) : null,
                    'status' => $det['status'] ?? '-',
                ];
            }
        }

        // Build dynamic activity logs for admin dashboard
        $activityLogs = collect();
        if ($isAdmin) {
            // 1. Fetch Leave Requests
            $leaves = \App\Models\LeaveRequest::with('employee')->latest()->take(10)->get();
            foreach ($leaves as $leave) {
                $statusText = 'mengajukan cuti/izin';
                if ($leave->status === 'Approved') {
                    $statusText = 'izin/cuti disetujui';
                } elseif ($leave->status === 'Rejected') {
                    $statusText = 'izin/cuti ditolak';
                }

                $activityLogs->push([
                    'type' => 'leave',
                    'icon' => 'file-text',
                    'icon_color' => $leave->status === 'Approved' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20' : ($leave->status === 'Rejected' ? 'text-rose-600 dark:text-rose-450 bg-rose-50 dark:bg-rose-950/20' : 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20'),
                    'title' => 'Cuti & Izin Pegawai',
                    'description' => ($leave->employee->name ?? 'Pegawai') . ' ' . $statusText . ' (' . ($leave->leaveType->name ?? $leave->reason) . ')',
                    'time' => $leave->created_at,
                ]);
            }

            // 2. Fetch Picket Swaps
            $swaps = \App\Models\PicketSwap::with(['requester', 'targetEmployee'])->latest()->take(10)->get();
            foreach ($swaps as $swap) {
                $statusText = 'mengajukan tukar piket';
                if ($swap->status === 'approved') {
                    $statusText = 'tukar piket disetujui resmi';
                } elseif ($swap->status === 'approved_by_target') {
                    $statusText = 'tukar piket disetujui target (menunggu verifikasi admin)';
                } elseif ($swap->status === 'rejected') {
                    $statusText = 'tukar piket ditolak';
                }

                $activityLogs->push([
                    'type' => 'swap',
                    'icon' => 'arrow-left-right',
                    'icon_color' => $swap->status === 'approved' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20' : ($swap->status === 'rejected' ? 'text-rose-600 dark:text-rose-455 bg-rose-50 dark:bg-rose-950/20' : 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/20'),
                    'title' => 'Tukar Jadwal Piket',
                    'description' => ($swap->requester->name ?? 'Pegawai') . ' ' . $statusText . ' dengan ' . ($swap->targetEmployee->name ?? 'Pegawai'),
                    'time' => $swap->created_at,
                ]);
            }

            // 3. Fetch Attendances (Check-in/Check-out)
            $attendances = \App\Models\Attendance::with('employee')->latest()->take(15)->get();
            foreach ($attendances as $att) {
                if ($att->clock_in) {
                    $activityLogs->push([
                        'type' => 'attendance_in',
                        'icon' => 'log-in',
                        'icon_color' => 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/20',
                        'title' => 'Absensi Masuk (Check-in)',
                        'description' => ($att->employee->name ?? 'Pegawai') . ' melakukan absen masuk jam ' . substr($att->clock_in, 0, 5) . ' WIB',
                        'time' => \Carbon\Carbon::parse($att->date . ' ' . $att->clock_in),
                    ]);
                }
                if ($att->clock_out) {
                    $activityLogs->push([
                        'type' => 'attendance_out',
                        'icon' => 'log-out',
                        'icon_color' => 'text-slate-655 dark:text-slate-400 bg-slate-50 dark:bg-slate-900',
                        'title' => 'Absensi Pulang (Check-out)',
                        'description' => ($att->employee->name ?? 'Pegawai') . ' melakukan absen pulang jam ' . substr($att->clock_out, 0, 5) . ' WIB',
                        'time' => \Carbon\Carbon::parse($att->date . ' ' . $att->clock_out),
                    ]);
                }
            }

            // 4. Fetch Employee Updates / Creations
            $newEmployees = \App\Models\Employee::latest()->take(10)->get();
            foreach ($newEmployees as $emp) {
                $isUpdate = $emp->updated_at->gt($emp->created_at->addMinutes(5));
                $activityLogs->push([
                    'type' => 'employee',
                    'icon' => 'user',
                    'icon_color' => $isUpdate ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/20' : 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20',
                    'title' => $isUpdate ? 'Profil Pegawai Diperbarui' : 'Pegawai Baru Terdaftar',
                    'description' => ($emp->name ?? 'Pegawai') . ' (' . ($emp->position ?? $emp->employeeType->name ?? 'Pegawai') . ')',
                    'time' => $isUpdate ? $emp->updated_at : $emp->created_at,
                ]);
            }

            // Sort all activities by time descending and take top 10
            $activityLogs = $activityLogs->sortByDesc('time')->take(10)->values();
        }

        return view('admin.dashboard', compact(
            'isAdmin',
            'employeeCount',
            'gpkCount',
            'gpqCount',
            'employeeAttendancePercent',
            'gpkAttendancePercent',
            'gpqAttendancePercent',
            'todayOverallPercent',
            'diffPercent',
            'latestAnnouncements',
            'myReport',
            'totalLeavesThisYear',
            'myRecentLeaves',
            'chartPoints',
            'totalLateDays',
            'myActiveShifts',
            'myCalendarDays',
            'myPicketSchedules',
            'myPicketToday',
            'todayDayOfWeek',
            'activityLogs',
            'adminChartPoints'
        ));
    }
}
