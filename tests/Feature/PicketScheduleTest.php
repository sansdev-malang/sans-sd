<?php

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Employee;
use App\Models\PicketArea;
use App\Models\PicketSchedule;
use App\Models\PicketSwap;
use App\Notifications\PicketSwapNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

test('guest cannot access picket schedules', function () {
    $response = $this->get('/picket-schedules');
    $response->assertRedirect('/login');
});

test('authenticated teacher can download picket schedules as PDF', function () {
    $teacher = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $user = User::factory()->create(['role' => 'employee', 'employee_id' => $teacher->id]);

    $response = $this->actingAs($user)->get('/picket-schedules/download');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('Content-Disposition', 'attachment; filename=Jadwal_Piket_Guru_SD.pdf');
});

test('teacher can view picket schedules but cannot access admin panel', function () {
    $teacher = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $user = User::factory()->create(['role' => 'employee', 'employee_id' => $teacher->id]);

    $response = $this->actingAs($user)->get('/picket-schedules');
    $response->assertOk();
    $response->assertSee('Jadwal Piket');

    // Should get 403 on admin dashboard
    $adminResponse = $this->actingAs($user)->get('/admin/picket-schedules');
    $adminResponse->assertStatus(403);
});

test('admin can access admin dashboard, manage areas and assignments', function () {
    $admin = User::factory()->create(['role' => 'admin_sd']);
    $teacher = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);

    $response = $this->actingAs($admin)->get('/admin/picket-schedules');
    $response->assertOk();

    // 1. Create Area
    $areaResponse = $this->actingAs($admin)->post('/admin/picket-schedules/areas', [
        'name' => 'Depan Koperasi',
        'duty_hours' => '06.30 - 07.00',
        'jobs' => "Menjaga Koperasi\nMenyapa Siswa",
    ]);
    $areaResponse->assertRedirect();
    $this->assertDatabaseHas('picket_areas', ['name' => 'Depan Koperasi']);

    $area = PicketArea::where('name', 'Depan Koperasi')->first();

    // 2. Create Assignment
    $assignResponse = $this->actingAs($admin)->post('/admin/picket-schedules/assignment', [
        'picket_area_id' => $area->id,
        'day_of_week' => 1, // Senin
        'employee_id' => $teacher->id,
    ]);
    $assignResponse->assertRedirect();
    $this->assertDatabaseHas('picket_schedules', [
        'picket_area_id' => $area->id,
        'day_of_week' => 1,
        'employee_id' => $teacher->id,
    ]);
});

test('teachers can request and approve swaps, and admin finalizes it with notifications', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin_sd']);
    
    $teacherA = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $userA = User::factory()->create(['role' => 'employee', 'employee_id' => $teacherA->id]);

    $teacherB = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $userB = User::factory()->create(['role' => 'employee', 'employee_id' => $teacherB->id]);

    $area = PicketArea::create([
        'name' => 'Gate 1',
        'duty_hours' => '06.30 - 07.00',
        'jobs' => 'Watch gate',
    ]);

    // Teacher A is scheduled on Monday (1)
    $schedA = PicketSchedule::create([
        'picket_area_id' => $area->id,
        'day_of_week' => 1,
        'employee_id' => $teacherA->id,
    ]);

    // Teacher B is scheduled on Tuesday (2)
    $schedB = PicketSchedule::create([
        'picket_area_id' => $area->id,
        'day_of_week' => 2,
        'employee_id' => $teacherB->id,
    ]);

    $mondayDate = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');
    $tuesdayDate = Carbon::now()->next(Carbon::TUESDAY)->format('Y-m-d');

    // 1. Teacher A requests swap with Teacher B
    $swapResponse = $this->actingAs($userA)->post('/picket-schedules/swap', [
        'requested_date' => $mondayDate,
        'target_employee_id' => $teacherB->id,
        'target_date' => $tuesdayDate,
        'notes' => 'Tolong tukar ya',
    ]);
    $swapResponse->assertRedirect();
    $this->assertDatabaseHas('picket_swaps', [
        'requester_id' => $teacherA->id,
        'target_employee_id' => $teacherB->id,
        'status' => 'pending',
    ]);

    $swap = PicketSwap::first();

    // Verify Notification sent to target teacher
    Notification::assertSentTo($userB, PicketSwapNotification::class);

    // Prevent duplicate swap request
    $dupResponse = $this->actingAs($userA)->post('/picket-schedules/swap', [
        'requested_date' => $mondayDate,
        'target_employee_id' => $teacherB->id,
        'target_date' => $tuesdayDate,
    ]);
    $dupResponse->assertSessionHas('error');

    // 2. Teacher B approves the swap
    $approveTargetResponse = $this->actingAs($userB)->post("/picket-schedules/swap/{$swap->id}/approve-target");
    $approveTargetResponse->assertRedirect();
    $this->assertEquals('approved_by_target', $swap->fresh()->status);

    // 3. Admin finalizes and approves the swap
    $approveAdminResponse = $this->actingAs($admin)->post("/admin/picket-schedules/swap/{$swap->id}/approve-admin");
    $approveAdminResponse->assertRedirect();
    $this->assertEquals('approved', $swap->fresh()->status);

    // Verify master schedules remain non-destructively intact
    $this->assertDatabaseHas('picket_schedules', [
        'picket_area_id' => $area->id,
        'day_of_week' => 1,
        'employee_id' => $teacherA->id,
    ]);
    $this->assertDatabaseHas('picket_schedules', [
        'picket_area_id' => $area->id,
        'day_of_week' => 2,
        'employee_id' => $teacherB->id,
    ]);
});

test('teacher cannot request swap with themselves', function () {
    $teacher = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $user = User::factory()->create(['role' => 'employee', 'employee_id' => $teacher->id]);

    $area = PicketArea::create([
        'name' => 'Gate 1',
        'duty_hours' => '06.30 - 07.00',
        'jobs' => 'Watch gate',
    ]);

    PicketSchedule::create([
        'picket_area_id' => $area->id,
        'day_of_week' => 1,
        'employee_id' => $teacher->id,
    ]);

    $mondayDate = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');
    $tuesdayDate = Carbon::now()->next(Carbon::TUESDAY)->format('Y-m-d');

    $response = $this->actingAs($user)
        ->from('/picket-schedules')
        ->post('/picket-schedules/swap', [
            'requested_date' => $mondayDate,
            'target_employee_id' => $teacher->id,
            'target_date' => $tuesdayDate,
            'notes' => 'Self swap attempt',
        ]);

    $response->assertRedirect('/picket-schedules');
    $response->assertSessionHasErrors('target_employee_id');
    $this->assertDatabaseMissing('picket_swaps', [
        'requester_id' => $teacher->id,
        'target_employee_id' => $teacher->id,
    ]);
});

test('admin can clone previous academic year picket schedules', function () {
    $admin = User::factory()->create(['role' => 'admin_sd']);

    $yearOld = AcademicYear::create([
        'name' => '2025/2026',
        'start_date' => '2025-07-01',
        'end_date' => '2026-06-30',
        'is_active' => false,
    ]);

    $yearNew = AcademicYear::create([
        'name' => '2026/2027',
        'start_date' => '2026-07-01',
        'end_date' => '2027-06-30',
        'is_active' => true,
    ]);

    $teacher = Employee::factory()->create(['unit' => 'sd', 'status' => 'Active']);
    $area = PicketArea::create([
        'name' => 'Gate Utama',
        'duty_hours' => '06.30 - 07.00',
        'is_active' => true,
    ]);

    // Old year schedule
    PicketSchedule::create([
        'picket_area_id' => $area->id,
        'day_of_week' => 1,
        'employee_id' => $teacher->id,
        'start_date' => '2025-07-01',
        'end_date' => '2026-06-30',
    ]);

    $response = $this->actingAs($admin)->post('/admin/picket-schedules/clone-year', [
        'source_academic_year_id' => $yearOld->id,
        'target_academic_year_id' => $yearNew->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify cloned schedule exists for new year
    $cloned = PicketSchedule::where('picket_area_id', $area->id)
        ->where('day_of_week', 1)
        ->where('employee_id', $teacher->id)
        ->where('start_date', 'like', '2026-07-01%')
        ->first();
    expect($cloned)->not->toBeNull();
});
