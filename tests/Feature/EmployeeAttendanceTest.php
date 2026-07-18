<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

test('guest cannot access employee or attendance routes', function () {
    $this->get(route('employees.index'))->assertRedirect('/login');
    $this->get(route('attendances.index'))->assertRedirect('/login');
});

test('can list employees with filters', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    $teacherType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    $employeeType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'employee'],
        ['name' => 'Karyawan']
    );

    // Create PAUD Teacher
    Employee::factory()->create([
        'name' => 'Guru PAUD',
        'employee_type_id' => $teacherType->id,
        'unit' => 'paud',
    ]);

    // Create SD Teacher
    Employee::factory()->create([
        'name' => 'Guru SD',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
    ]);

    // Create SMP Employee
    Employee::factory()->create([
        'name' => 'Karyawan SMP',
        'employee_type_id' => $employeeType->id,
        'unit' => 'smp',
    ]);

    // Test list all
    $response = $this->actingAs($user)->getJson(route('employees.index'));
    $response->assertOk()->assertJsonCount(3, 'data');

    // Test type filter
    $response = $this->actingAs($user)->getJson(route('employees.index', ['type' => 'teacher']));
    $response->assertOk()->assertJsonCount(2, 'data');

    // Test unit filter
    $response = $this->actingAs($user)->getJson(route('employees.index', ['unit' => 'smp']));
    $response->assertOk()->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Karyawan SMP');
});

test('can CRUD employee', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    $teacherType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    // Create
    $response = $this->actingAs($user)->postJson(route('employees.store'), [
        'name' => 'John Doe',
        'email' => 'john@sans.dev',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
        'gender' => 'Male',
        'zkteco_uid' => '999',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('employees', ['name' => 'John Doe', 'zkteco_uid' => '999']);

    $employeeId = $response->json('data.id');

    // Update
    $response = $this->actingAs($user)->putJson(route('employees.update', $employeeId), [
        'name' => 'John Update',
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('employees', ['id' => $employeeId, 'name' => 'John Update']);

    // Delete
    $response = $this->actingAs($user)->deleteJson(route('employees.destroy', $employeeId));
    $response->assertOk();
    $this->assertDatabaseMissing('employees', ['id' => $employeeId]);
});

test('verifies attendance filter by unit', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);
    $today = Carbon::today()->toDateString();

    $teacherType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    $employeeType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'employee'],
        ['name' => 'Karyawan']
    );

    // 1. Create PAUD Employee and attendance
    $paudEmp = Employee::factory()->create([
        'name' => 'Staf PAUD',
        'employee_type_id' => $employeeType->id,
        'unit' => 'paud',
    ]);
    Attendance::create([
        'employee_id' => $paudEmp->id,
        'date' => $today,
        'status' => 'Present',
    ]);

    // 2. Create SD Employee and attendance
    $sdEmp = Employee::factory()->create([
        'name' => 'Guru SD',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
    ]);
    Attendance::create([
        'employee_id' => $sdEmp->id,
        'date' => $today,
        'status' => 'Present',
    ]);

    // 3. Create SMP Employee and attendance
    $smpEmp = Employee::factory()->create([
        'name' => 'Guru SMP',
        'employee_type_id' => $teacherType->id,
        'unit' => 'smp',
    ]);
    Attendance::create([
        'employee_id' => $smpEmp->id,
        'date' => $today,
        'status' => 'Sick',
    ]);

    // A. Query recap for PAUD - should only return PAUD
    $response = $this->actingAs($user)->getJson(route('attendances.recap', ['unit' => 'paud']));
    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.employee.name', 'Staf PAUD');

    // B. Query recap for SD - should only return SD
    $response = $this->actingAs($user)->getJson(route('attendances.recap', ['unit' => 'sd']));
    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.employee.name', 'Guru SD');

    // C. Query recap for SMP - should only return SMP
    $response = $this->actingAs($user)->getJson(route('attendances.recap', ['unit' => 'smp']));
    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.employee.name', 'Guru SMP');
});

test('verifies employee template download', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    $response = $this->actingAs($user)->get(route('employees.download-template'));
    $response->assertOk();
    $response->assertHeader('Content-Disposition', 'attachment; filename=template_pegawai.xlsx');
});

test('verifies employee excel import', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    // Create an in-memory XLSX file
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nama Lengkap');
    $sheet->setCellValue('B1', 'Email');
    $sheet->setCellValue('C1', 'NUPTK/NIP/NIK');
    $sheet->setCellValue('D1', 'Tipe Pegawai');
    $sheet->setCellValue('E1', 'Unit Sekolah');
    $sheet->setCellValue('F1', 'Jabatan');
    $sheet->setCellValue('G1', 'Jenis Kelamin');
    $sheet->setCellValue('H1', 'Status Kepegawaian');
    $sheet->setCellValue('I1', 'PIN ZK');
    $sheet->setCellValue('J1', 'Status');

    $sheet->setCellValue('A2', 'Pegawai Baru Excel');
    $sheet->setCellValue('B2', 'excel@example.com');
    $sheet->setCellValue('C2', '99999999');
    $sheet->setCellValue('D2', 'teacher');
    $sheet->setCellValue('E2', 'sd');
    $sheet->setCellValue('F2', 'Math');
    $sheet->setCellValue('G2', 'Male');
    $sheet->setCellValue('H2', 'PNS');
    $sheet->setCellValue('I2', '9999');
    $sheet->setCellValue('J2', 'Active');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Save to temp path
    $tempPath = tempnam(sys_get_temp_dir(), 'test_import') . '.xlsx';
    $writer->save($tempPath);

    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $tempPath,
        'template_pegawai.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($user)->post(route('employees.import'), [
        'file' => $uploadedFile
    ]);

    $response->assertRedirect(route('employees.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('employees', [
        'name' => 'Pegawai Baru Excel',
        'email' => 'excel@example.com',
        'zkteco_uid' => 9999,
    ]);

    @unlink($tempPath);
});

test('verifies employee unit scoping via school_unit config', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    // Set configuration dynamically
    config(['app.school_unit' => 'sd']);

    $teacherType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    // Create SD Employee
    $sdEmp = Employee::factory()->create([
        'name' => 'Guru SD Asli',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
    ]);

    // Create SMP Employee
    $smpEmp = Employee::factory()->create([
        'name' => 'Guru SMP Asli',
        'employee_type_id' => $teacherType->id,
        'unit' => 'smp',
    ]);

    // Verify index only returns SD employee
    $response = $this->actingAs($user)->getJson(route('employees.index'));
    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Guru SD Asli');

    // Verify store automatically scopes to SD even if payload says SMP
    $response = $this->actingAs($user)->postJson(route('employees.store'), [
        'name' => 'Tamu SMP',
        'email' => 'tamu@smp.dev',
        'employee_type_id' => $teacherType->id,
        'unit' => 'smp', // try to set to smp
        'gender' => 'Male',
        'zkteco_uid' => '8888',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('employees', [
        'name' => 'Tamu SMP',
        'unit' => 'sd', // should be overridden to sd
    ]);
});

test('employee role is forbidden from admin routes', function () {
    $user = User::factory()->create(['role' => 'employee']);

    $this->actingAs($user)->get(route('employees.index'))->assertStatus(403);
    $this->actingAs($user)->get(route('settings'))->assertStatus(403);
});

test('employee can view their own attendance', function () {
    $teacherType = \App\Models\EmployeeType::firstOrCreate(
        ['code' => 'teacher'],
        ['name' => 'Guru']
    );

    // Create physical employee
    $employee = Employee::factory()->create([
        'name' => 'Guru Tester',
        'employee_type_id' => $teacherType->id,
    ]);

    // Create user account linked to that employee
    $user = User::factory()->create([
        'role' => 'employee',
        'employee_id' => $employee->id,
    ]);

    // Create attendance logs for this employee and another employee
    Attendance::create([
        'employee_id' => $employee->id,
        'date' => Carbon::now()->startOfMonth()->toDateString(),
        'status' => 'Present',
    ]);

    $anotherEmployee = Employee::factory()->create([
        'name' => 'Another Guru',
        'employee_type_id' => $teacherType->id,
    ]);

    Attendance::create([
        'employee_id' => $anotherEmployee->id,
        'date' => Carbon::now()->startOfMonth()->toDateString(),
        'status' => 'Sick',
    ]);

    // Request personal attendance
    $response = $this->actingAs($user)->get(route('my-attendance'));
    $response->assertOk();
    // It should see the current user's name or attendance details
    $response->assertSee($user->name);
});
