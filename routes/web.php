<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ZktecoDeviceController;
use App\Http\Controllers\PicketScheduleController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassLevelController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SpmbCandidateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/offline', 'errors.offline')->name('offline');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Academic Master & Student Management (English Resource Standard)
Route::middleware(['auth', 'verified', 'role:super_admin,admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->group(function () {
    // Academic Years (Tahun Ajaran)
    Route::post('academic-years/{id}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
    Route::match(['post', 'put', 'patch'], 'academic-years/{id}', [AcademicYearController::class, 'update'])->name('academic-years.update-custom');
    Route::match(['post', 'delete'], 'academic-years/{id}/delete', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy-custom');
    Route::resource('academic-years', AcademicYearController::class);

    // Semesters (Semester)
    Route::post('semesters/{id}/set-active', [\App\Http\Controllers\SemesterController::class, 'setActive'])->name('semesters.set-active');
    Route::match(['post', 'put', 'patch'], 'semesters/{id}', [\App\Http\Controllers\SemesterController::class, 'update'])->name('semesters.update-custom');
    Route::match(['post', 'delete'], 'semesters/{id}/delete', [\App\Http\Controllers\SemesterController::class, 'destroy'])->name('semesters.destroy-custom');
    Route::resource('semesters', \App\Http\Controllers\SemesterController::class);

    // Class Levels (Tingkat Kelas)
    Route::resource('class-levels', ClassLevelController::class);

    // Classrooms (Rombongan Belajar)
    Route::get('/classrooms/{id}/students', [ClassroomController::class, 'students'])->name('classrooms.students');
    Route::resource('classrooms', ClassroomController::class);
    Route::get('/rombel', fn() => redirect()->route('classrooms.index'))->name('rombel');

    // Students (Data Siswa & Bulk Excel Import)
    Route::get('students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students/export/excel', [StudentController::class, 'exportExcel'])->name('students.export.excel');
    Route::get('students/export/pdf', [StudentController::class, 'exportPdf'])->name('students.export.pdf');
    Route::get('students/print', [StudentController::class, 'print'])->name('students.print');
    Route::resource('students', StudentController::class);
    Route::get('/siswa', fn() => redirect()->route('students.index'))->name('siswa');

    // Homeroom & Teacher Assignments (Penugasan Wali Kelas & Formasi Guru)
    Route::post('homeroom-assignments/{id}/toggle-status', [\App\Http\Controllers\HomeroomAssignmentController::class, 'toggleStatus'])->name('homeroom-assignments.toggle-status');
    Route::resource('homeroom-assignments', \App\Http\Controllers\HomeroomAssignmentController::class);
    Route::get('/wali-kelas', fn() => redirect()->route('homeroom-assignments.index'))->name('wali-kelas');

    // Student & Classroom Distribution Report (Rekapitulasi Rombel & Kesiswaan - Sheet 2 TU)
    Route::get('student-reports', [\App\Http\Controllers\StudentReportController::class, 'index'])->name('student-reports.index');
    Route::get('student-reports/print', [\App\Http\Controllers\StudentReportController::class, 'print'])->name('student-reports.print');
    Route::get('student-reports/export', [\App\Http\Controllers\StudentReportController::class, 'export'])->name('student-reports.export');

    // Class Promotions & Graduation (Kenaikan Kelas & Kelulusan)
    Route::get('class-promotions', [\App\Http\Controllers\ClassPromotionController::class, 'index'])->name('promotions.index');
    Route::get('class-promotions/students', [\App\Http\Controllers\ClassPromotionController::class, 'getStudents'])->name('promotions.students');
    Route::post('class-promotions/process', [\App\Http\Controllers\ClassPromotionController::class, 'promote'])->name('promotions.process');
    Route::post('class-promotions/graduate', [\App\Http\Controllers\ClassPromotionController::class, 'graduate'])->name('promotions.graduate');

    // Alumni & Graduation Ledger
    Route::get('alumni', [\App\Http\Controllers\AlumniController::class, 'index'])->name('alumni.index');
    Route::get('alumni/{id}', [\App\Http\Controllers\AlumniController::class, 'show'])->name('alumni.show');
    Route::get('alumni/{id}/print', [\App\Http\Controllers\AlumniController::class, 'print'])->name('alumni.print');
    Route::put('alumni/{id}', [\App\Http\Controllers\AlumniController::class, 'update'])->name('alumni.update');
});



// Route Leave Actions
Route::post('/leaves/{id}/approve', [\App\Http\Controllers\LeaveRequestController::class, 'approve'])->middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->name('leaves.approve');
Route::post('/leaves/{id}/reject', [\App\Http\Controllers\LeaveRequestController::class, 'reject'])->middleware(['auth', 'verified', 'role:admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->name('leaves.reject');


// Rekap & Hasil Rapor (1-Click SSO to SANS Rapor)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/rapor', function () {
        $ssoSecret = \App\Models\Setting::get('rapor_sso_secret', env('SSO_SECRET_KEY', 'sans_rapor_secret_sso_key_2026'));
        $raporUrl = \App\Models\Setting::get('rapor_url', env('SANS_RAPOR_URL', 'http://sans-rapor.test'));
        $curUser = auth()->user();
        $isAdmin = in_array($curUser?->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp']);
        $ssoPayload = base64_encode(json_encode([
            'id' => $curUser?->id,
            'name' => $curUser?->name,
            'email' => $curUser?->email,
            'employee_id' => $curUser?->employee_id,
            'role' => $curUser?->role ?? ($isAdmin ? 'super_admin' : 'guru'),
            'unit' => 'sd',
            'timestamp' => time(),
        ]));
        $ssoSig = hash_hmac('sha256', $ssoPayload, $ssoSecret);
        return redirect("{$raporUrl}/sso/login?data=" . urlencode($ssoPayload) . "&signature=" . urlencode($ssoSig));
    })->name('report-cards.index');

    Route::get('report-cards', fn() => redirect()->route('report-cards.index'));
    Route::get('report-cards/{any}', fn() => redirect()->route('report-cards.index'))->where('any', '.*');
});

// SPMB Webhook Receiver (Real-time Push)
Route::post('/api/spmb-webhook', [\App\Http\Controllers\Api\SpmbWebhookController::class, 'handleWebhook'])->name('api.spmb-webhook');

Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-spmb-connection', [SettingController::class, 'testSpmbConnection'])->name('spmb.test-connection');
    Route::post('/settings/test-rapor-connection', [SettingController::class, 'testRaporConnection'])->name('settings.test-rapor');
    Route::resource('users', \App\Http\Controllers\UserController::class);
});

Route::middleware(['auth', 'verified', 'role:super_admin,admin_sd,admin_paud,admin_smp,kepala_sekolah,waka'])->group(function () {

    // SPMB New Candidate Management
    Route::prefix('spmb')->name('spmb.')->group(function () {
        Route::get('/pendaftar', [SpmbCandidateController::class, 'index'])->name('candidates.index');
        Route::get('/pendaftar/{id}', [SpmbCandidateController::class, 'show'])->name('candidates.show');
        Route::put('/pendaftar/{id}', [SpmbCandidateController::class, 'update'])->name('candidates.update');
        Route::delete('/pendaftar/{id}', [SpmbCandidateController::class, 'destroy'])->name('candidates.destroy');
        Route::post('/pendaftar/sync', [SpmbCandidateController::class, 'sync'])->name('candidates.sync');
        Route::get('/pendaftar/{id}/enroll-data', [SpmbCandidateController::class, 'getEnrollData'])->name('candidates.enroll-data');
        Route::post('/pendaftar/{id}/enroll', [SpmbCandidateController::class, 'enroll'])->name('candidates.enroll');
        Route::post('/pendaftar/{id}/unenroll', [SpmbCandidateController::class, 'unenroll'])->name('candidates.unenroll');
    });

    // New English singular based routes
    Route::get('teachers/download-template', [\App\Http\Controllers\TeacherController::class, 'downloadTemplate'])->name('teachers.download-template');
    Route::post('teachers/import', [\App\Http\Controllers\TeacherController::class, 'import'])->name('teachers.import');
    Route::resource('teachers', \App\Http\Controllers\TeacherController::class);

    Route::get('employees/download-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.download-template');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::post('employees/generate-accounts', [EmployeeController::class, 'generateAccounts'])->name('employees.generate-accounts');
    Route::post('employees/{employee}/generate-account', [EmployeeController::class, 'generateSingleAccount'])->name('employees.generate-account');
    Route::post('employees/sync-cache', [EmployeeController::class, 'syncCache'])->name('employees.sync-cache');
    Route::get('employees/export/excel', [EmployeeController::class, 'exportExcel'])->name('employees.export.excel');
    Route::get('employees/export/pdf', [EmployeeController::class, 'exportPdf'])->name('employees.export.pdf');
    Route::resource('employees', EmployeeController::class);
    Route::resource('employee-types', EmployeeTypeController::class);
    Route::resource('leave-types', \App\Http\Controllers\LeaveTypeController::class);
    Route::resource('attendances', AttendanceController::class)->except(['index', 'show']);
    Route::resource('leaves', \App\Http\Controllers\LeaveRequestController::class);
    Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class)->except(['index', 'show']);

    // Admin picket schedule routes
    Route::get('admin/picket-schedules', [PicketScheduleController::class, 'adminDashboard'])->name('picket-schedules.admin');
    Route::post('admin/picket-schedules/assignment', [PicketScheduleController::class, 'storeAssignment'])->name('picket-schedules.assignment.store');
    Route::delete('admin/picket-schedules/assignment/{id}', [PicketScheduleController::class, 'destroyAssignment'])->name('picket-schedules.assignment.destroy');
    Route::post('admin/picket-schedules/clone-year', [PicketScheduleController::class, 'clonePreviousYearSchedules'])->name('picket-schedules.clone-year');
    Route::post('admin/picket-schedules/areas', [PicketScheduleController::class, 'storeArea'])->name('picket-schedules.areas.store');
    Route::put('admin/picket-schedules/areas/{id}', [PicketScheduleController::class, 'updateArea'])->name('picket-schedules.areas.update');
    Route::delete('admin/picket-schedules/areas/{id}', [PicketScheduleController::class, 'destroyArea'])->name('picket-schedules.areas.destroy');
    Route::post('admin/picket-schedules/swap/{id}/approve-admin', [PicketScheduleController::class, 'approveSwapAdmin'])->name('picket-schedules.swap.approve-admin');
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

    // Teacher/Employee picket schedule routes
    Route::get('picket-schedules', [PicketScheduleController::class, 'index'])->name('picket-schedules.index');
    Route::get('picket-schedules/download', [PicketScheduleController::class, 'downloadPdf'])->name('picket-schedules.download');
    Route::post('picket-schedules/swap', [PicketScheduleController::class, 'requestSwap'])->name('picket-schedules.swap.request');
    Route::post('picket-schedules/swap/{id}/approve-target', [PicketScheduleController::class, 'approveSwapTarget'])->name('picket-schedules.swap.approve-target');
    Route::post('picket-schedules/swap/{id}/reject', [PicketScheduleController::class, 'rejectSwap'])->name('picket-schedules.swap.reject');
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
    Route::get('leave-types', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveTypes']);

    // Shifts, schedules, holidays, bonuses, and leaves sync endpoints
    Route::post('sync/shifts', [\App\Http\Controllers\Api\HrdApiController::class, 'syncShifts']);
    Route::post('sync/schedules', [\App\Http\Controllers\Api\HrdApiController::class, 'syncSchedules']);
    Route::post('sync/holidays', [\App\Http\Controllers\Api\HrdApiController::class, 'syncHolidays']);
    Route::post('sync/bonus-schemas', [\App\Http\Controllers\Api\HrdApiController::class, 'syncBonusSchemas']);
    Route::post('sync/announcements', [\App\Http\Controllers\Api\HrdApiController::class, 'syncAnnouncements']);
    Route::post('sync/payslips', [\App\Http\Controllers\Api\HrdApiController::class, 'syncPayslip']);
    Route::post('sync/leave-types', [\App\Http\Controllers\Api\HrdApiController::class, 'syncLeaveType']);
    Route::get('leave-requests', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveRequests']);
    Route::post('leave-requests/decision', [\App\Http\Controllers\Api\HrdApiController::class, 'leaveDecision']);
    Route::get('picket-assignments', [\App\Http\Controllers\Api\HrdApiController::class, 'picketAssignments']);
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::post('zkteco-devices/{zktecoDevice}/ping', [ZktecoDeviceController::class, 'ping'])->name('zkteco-devices.ping');
    Route::resource('zkteco-devices', ZktecoDeviceController::class);

    // System Logs
    Route::get('system-logs', [\App\Http\Controllers\SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('system-logs/download', [\App\Http\Controllers\SystemLogController::class, 'download'])->name('system-logs.download');
    Route::post('system-logs/clear', [\App\Http\Controllers\SystemLogController::class, 'clear'])->name('system-logs.clear');
    Route::delete('system-logs/delete', [\App\Http\Controllers\SystemLogController::class, 'destroy'])->name('system-logs.destroy');
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
    Route::get('payslips', [\App\Http\Controllers\PayslipController::class, 'index'])->name('payslips.index');
});

// Profil Pegawai (Normal User)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-employee-profile', [App\Http\Controllers\MyEmployeeProfileController::class, 'edit'])->name('my-employee-profile.edit');
    Route::put('/my-employee-profile', [App\Http\Controllers\MyEmployeeProfileController::class, 'update'])->name('my-employee-profile.update');
});
