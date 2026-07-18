<?php

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Configure API token for testing
    config(['app.hrd_api_token' => 'test_token']);
});

test('api returns 401 when token is missing or invalid', function () {
    $this->getJson('/api/v1/hrd/employees')
        ->assertStatus(401)
        ->assertJsonPath('success', false);

    $this->getJson('/api/v1/hrd/employees', ['X-API-TOKEN' => 'wrong_token'])
        ->assertStatus(401)
        ->assertJsonPath('success', false);
});

test('api returns employees list when token is valid', function () {
    $teacherType = EmployeeType::firstOrCreate(['code' => 'teacher'], ['name' => 'Guru']);

    Employee::factory()->create([
        'name' => 'Guru SD',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
    ]);

    $response = $this->getJson('/api/v1/hrd/employees', ['X-API-TOKEN' => 'test_token']);
    
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Guru SD');
});

test('api filters employees and attendances by active school unit', function () {
    $teacherType = EmployeeType::firstOrCreate(['code' => 'teacher'], ['name' => 'Guru']);

    // Set SCHOOL_UNIT to 'sd'
    config(['app.school_unit' => 'sd']);

    // Create SD employee and log
    $sdEmp = Employee::factory()->create([
        'name' => 'Guru SD',
        'employee_type_id' => $teacherType->id,
        'unit' => 'sd',
    ]);
    Attendance::create([
        'employee_id' => $sdEmp->id,
        'date' => Carbon::today()->toDateString(),
        'status' => 'Present',
    ]);

    // Create SMP employee and log
    $smpEmp = Employee::factory()->create([
        'name' => 'Guru SMP',
        'employee_type_id' => $teacherType->id,
        'unit' => 'smp',
    ]);
    Attendance::create([
        'employee_id' => $smpEmp->id,
        'date' => Carbon::today()->toDateString(),
        'status' => 'Present',
    ]);

    // Retrieve employees API
    $empResponse = $this->getJson('/api/v1/hrd/employees', ['X-API-TOKEN' => 'test_token']);
    $empResponse->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Guru SD');

    // Retrieve attendances API
    $attResponse = $this->getJson('/api/v1/hrd/attendances', ['X-API-TOKEN' => 'test_token']);
    $attResponse->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.employee.name', 'Guru SD');
});
