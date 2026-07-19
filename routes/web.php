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
    return view('welcome');
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
    $employeeCount = \App\Models\Employee::count();
    return view('admin.dashboard', compact('employeeCount'));
})->middleware(['auth', 'verified'])->name('dashboard');



// Route Absensi
Route::get('/absensi_hari_ini', function () {
    return view('admin.absensi_hari_ini');
})->middleware(['auth', 'verified'])->name('absensi_hari_ini');

Route::get('/absensi_laporan', function () {
    return view('admin.absensi_laporan');
})->middleware(['auth', 'verified'])->name('absensi_laporan');

Route::get('/absensi_riwayat', function () {
    return view('admin.absensi_riwayat');
})->middleware(['auth', 'verified'])->name('absensi_riwayat');

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
    Route::get('attendances/recap', [AttendanceController::class, 'recap'])->name('attendances.recap');
    Route::resource('employees', EmployeeController::class);
    Route::resource('employee-types', EmployeeTypeController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('leaves', \App\Http\Controllers\LeaveRequestController::class);
    Route::get('leave-history', [\App\Http\Controllers\LeaveRequestController::class, 'history'])->name('leave-history.index');
});

Route::middleware(['auth', 'verified', 'role:employee,admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->group(function () {
    Route::get('my-attendance', [\App\Http\Controllers\MyAttendanceController::class, 'index'])->name('my-attendance');
    Route::resource('my-leaves', \App\Http\Controllers\MyLeaveRequestController::class);
});

// REST API for HRD Central Aggregator Integration
Route::middleware('hrd.api')->prefix('api/v1/hrd')->group(function () {
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
    Route::get('leave-requests', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveRequests']);
    Route::post('leave-requests/decision', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveDecision']);
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::post('zkteco-devices/{zktecoDevice}/ping', [ZktecoDeviceController::class, 'ping'])->name('zkteco-devices.ping');
    Route::resource('zkteco-devices', ZktecoDeviceController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
