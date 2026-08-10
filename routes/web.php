<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ZktecoDeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/siswa', function () {
    return view('admin.siswa');
})->middleware(['auth', 'verified'])->name('siswa');

Route::get('/guru', function () {
    return view('admin.guru');
})->middleware(['auth', 'verified'])->name('guru');

Route::get('/rombel', function () {
    return view('admin.rombel');
})->middleware(['auth', 'verified'])->name('rombel');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $isAdmin = in_array($user->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);

    $employeeCount = \App\Models\Employee::count();
    
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
                $response = \Illuminate\Support\Facades\Http::timeout(15)->get(rtrim($hrdUrl, '/') . '/api/bonus-reports', [
                    'month' => date('Y-m'),
                    'unit_id' => strtolower($schoolUnit)
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
        // Filter out Pending days
        $completedDetails = array_filter($dailyDetails, function ($day) {
            return isset($day['status']) && $day['status'] !== 'Pending';
        });
        
        $idx = 0;
        foreach ($completedDetails as $dateStr => $det) {
            $x = $idx * 60; // 60px spacing per day
            $y = 130;
            $timeStr = '-';
            
            $jamMasuk = $det['check_in'] ?? null;
            if ($jamMasuk) {
                // Time calculations for chart Y position (06:00 = top, 08:00 = bottom)
                $parts = explode(':', $jamMasuk);
                $mins = (int)$parts[0] * 60 + (int)$parts[1];
                
                // 360 mins (06:00) -> Y=30. 480 mins (08:00) -> Y=130
                $y = 30 + (($mins - 360) * (100 / 120));
                if ($y < 30) $y = 30; 
                if ($y > 130) $y = 130; 
                
                $timeStr = substr($jamMasuk, 0, 5);
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

    return view('admin.dashboard', compact(
        'isAdmin',
        'employeeCount',
        'latestAnnouncements',
        'myReport',
        'totalLeavesThisYear',
        'myRecentLeaves',
        'chartPoints',
        'totalLateDays',
        'myActiveShifts'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');



// Route Absensi
Route::get('/absensi_hari_ini', function () {
    return view('admin.absensi_hari_ini');
})->middleware(['auth', 'verified'])->name('absensi_hari_ini');

Route::get('/absensi_laporan', function () {
    return view('admin.absensi_laporan');
})->middleware(['auth', 'verified'])->name('absensi_laporan');



Route::get('/absensi_izin_cuti', function () {
    return view('admin.absensi_izin_cuti');
})->middleware(['auth', 'verified'])->name('absensi_izin_cuti');

Route::get('/leave-approvals', [\App\Http\Controllers\LeaveApprovalController::class, 'index'])->middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->name('leave-approvals.index');
Route::post('/leave-approvals/{id}/approve', [\App\Http\Controllers\LeaveApprovalController::class, 'approve'])->middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->name('leave-approvals.approve');
Route::post('/leave-approvals/{id}/reject', [\App\Http\Controllers\LeaveApprovalController::class, 'reject'])->middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->name('leave-approvals.reject');

Route::get('/absensi_mesin', function () {
    return view('admin.absensi_mesin');
})->middleware(['auth', 'verified', 'role:super_admin'])->name('absensi_mesin');

Route::get('/absensi_log_penarikan', function () {
    return view('admin.absensi_log_penarikan');
})->middleware(['auth', 'verified', 'role:super_admin'])->name('absensi_log_penarikan');

Route::get('/absensi_shift', function () {
    return view('admin.absensi_shift');
})->middleware(['auth', 'verified'])->name('absensi_shift');

Route::get('/absensi_libur', function () {
    return view('admin.absensi_libur');
})->middleware(['auth', 'verified'])->name('absensi_libur');

Route::get('/absensi_bonus_denda', function () {
    return view('admin.absensi_bonus_denda');
})->middleware(['auth', 'verified'])->name('absensi_bonus_denda');

Route::get('/absensi_karyawan', function () {
    return view('admin.absensi_karyawan');
})->middleware(['auth', 'verified'])->name('absensi_karyawan');



// Route Homebase
Route::get('/homebase_leaderboard', function () {
    return view('admin.homebase_leaderboard');
})->middleware(['auth', 'verified'])->name('homebase_leaderboard');

Route::get('/homebase_merah', function () {
    return view('admin.homebase_merah');
})->middleware(['auth', 'verified'])->name('homebase_merah');

Route::get('/homebase_kuning', function () {
    return view('admin.homebase_kuning');
})->middleware(['auth', 'verified'])->name('homebase_kuning');

Route::get('/homebase_hijau', function () {
    return view('admin.homebase_hijau');
})->middleware(['auth', 'verified'])->name('homebase_hijau');

Route::get('/homebase_biru', function () {
    return view('admin.homebase_biru');
})->middleware(['auth', 'verified'])->name('homebase_biru');

Route::get('/homebase_ungu', function () {
    return view('admin.homebase_ungu');
})->middleware(['auth', 'verified'])->name('homebase_ungu');

Route::get('/form_homebase', function () {
    return view('admin.form_homebase');
})->middleware(['auth', 'verified'])->name('form_homebase');

Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('users', \App\Http\Controllers\UserController::class);
});

Route::middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->group(function () {

    // New English singular based routes
    Route::get('teachers/download-template', [\App\Http\Controllers\TeacherController::class, 'downloadTemplate'])->name('teachers.download-template');
    Route::post('teachers/import', [\App\Http\Controllers\TeacherController::class, 'import'])->name('teachers.import');
    Route::resource('teachers', \App\Http\Controllers\TeacherController::class);

    Route::get('employees/download-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.download-template');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::post('employees/generate-accounts', [EmployeeController::class, 'generateAccounts'])->name('employees.generate-accounts');
    Route::post('employees/{employee}/generate-account', [EmployeeController::class, 'generateSingleAccount'])->name('employees.generate-account');
    Route::resource('employees', EmployeeController::class);
    Route::resource('employee-types', EmployeeTypeController::class);
    Route::resource('leave-types', \App\Http\Controllers\LeaveTypeController::class);
    Route::resource('attendances', AttendanceController::class)->except(['index', 'show']);
    Route::resource('leaves', \App\Http\Controllers\LeaveRequestController::class);
    Route::get('leave-history', [\App\Http\Controllers\LeaveRequestController::class, 'history'])->name('leave-history.index');
    Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'verified', 'role:employee,admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->group(function () {
    Route::get('attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/export', [\App\Http\Controllers\AttendanceController::class, 'export'])->name('attendances.export');
    Route::get('bonus-reports', [\App\Http\Controllers\BonusReportController::class, 'index'])->name('bonus-reports.index');
    Route::get('bonus-reports/export', [\App\Http\Controllers\BonusReportController::class, 'export'])->name('bonus-reports.export');
    Route::get('my-attendance', [\App\Http\Controllers\MyAttendanceController::class, 'index'])->name('my-attendance');
    Route::resource('my-leaves', \App\Http\Controllers\MyLeaveRequestController::class);
    Route::get('announcements/{announcement}/download', [\App\Http\Controllers\AnnouncementController::class, 'download'])->name('announcements.download');
    Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class)->only(['index', 'show']);

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? url('/dashboard'));
    })->name('notifications.read');
});

// REST API for HRD Central Aggregator Integration
Route::middleware('hrd.api')->prefix('api/v1/hrd')->group(function () {
    Route::post('auth/verify', [\App\Http\Controllers\Api\HrdApiController::class, 'verify']);
    Route::get('employees', [\App\Http\Controllers\Api\HrdApiController::class, 'employees']);
    Route::post('employees', [\App\Http\Controllers\Api\HrdApiController::class, 'store']);
    Route::put('employees/{id}', [\App\Http\Controllers\Api\HrdApiController::class, 'update']);
    Route::delete('employees/{id}', [\App\Http\Controllers\Api\HrdApiController::class, 'destroy']);
    Route::get('attendances', [\App\Http\Controllers\Api\HrdApiController::class, 'attendances']);
    Route::get('employee-types', [\App\Http\Controllers\Api\HrdApiController::class, 'employeeTypes']);
    
    // Shifts, schedules, holidays, bonuses, and leaves sync endpoints
    Route::post('sync/shifts', [\App\Http\Controllers\Api\HrdApiController::class, 'syncShifts']);
    Route::post('sync/schedules', [\App\Http\Controllers\Api\HrdApiController::class, 'syncSchedules']);
    Route::post('sync/holidays', [\App\Http\Controllers\Api\HrdApiController::class, 'syncHolidays']);
    Route::post('sync/bonus-schemas', [\App\Http\Controllers\Api\HrdApiController::class, 'syncBonusSchemas']);
    Route::post('sync/announcements', [\App\Http\Controllers\Api\HrdApiController::class, 'syncAnnouncements']);
    Route::post('sync/payslips', [\App\Http\Controllers\Api\HrdApiController::class, 'syncPayslip']);
    Route::get('leave-requests', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveRequests']);
    Route::post('leave-requests/decision', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveDecision']);
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::post('zkteco-devices/{zktecoDevice}/ping', [ZktecoDeviceController::class, 'ping'])->name('zkteco-devices.ping');
    Route::resource('zkteco-devices', ZktecoDeviceController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/coming-soon', function () { return view('admin.coming-soon'); })->name('coming-soon');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Slip Gaji
Route::middleware(['auth', 'verified', 'role:employee,kepala_sekolah,waka,admin_sd,admin_paud,admin_smp,super_admin'])->group(function () {
    Route::get('attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/export', [\App\Http\Controllers\AttendanceController::class, 'export'])->name('attendances.export');
    Route::get('bonus-reports', [\App\Http\Controllers\BonusReportController::class, 'index'])->name('bonus-reports.index');
    Route::get('bonus-reports/export', [\App\Http\Controllers\BonusReportController::class, 'export'])->name('bonus-reports.export');
    Route::get('payslips', [\App\Http\Controllers\PayslipController::class, 'index'])->name('payslips.index');
});

// Profil Pegawai (Normal User)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-employee-profile', [App\Http\Controllers\MyEmployeeProfileController::class, 'edit'])->name('my-employee-profile.edit');
    Route::put('/my-employee-profile', [App\Http\Controllers\MyEmployeeProfileController::class, 'update'])->name('my-employee-profile.update');
});
