<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\WorkingShift;
use App\Models\WorkingShiftDetail;
use App\Models\EmployeeWorkingShift;
use App\Models\Holiday;
use App\Models\HolidayAdjustment;
use App\Models\BonusSchema;
use App\Models\BonusTier;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HrdApiController extends Controller
{
    /**
     * Get all employees for the active school unit.
     */
    public function employees()
    {
        $schoolUnit = config('app.school_unit');
        
        $query = Employee::with('employeeType');
        
        if ($schoolUnit) {
            $query->where('unit', $schoolUnit);
        }
        
        $employees = $query->get();
        
        return response()->json([
            'success' => true,
            'unit' => $schoolUnit ?? 'all',
            'data' => $employees
        ]);
    }

    /**
     * Get all attendances for the active school unit on a specific date.
     */
    public function attendances(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $schoolUnit = config('app.school_unit');
        
        $query = Attendance::where('date', $date)->with(['employee.employeeType']);
        
        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }
        
        $attendances = $query->get();
        
        return response()->json([
            'success' => true,
            'unit' => $schoolUnit ?? 'all',
            'date' => $date,
            'data' => $attendances
        ]);
    }

    /**
     * Store a newly created employee in the school unit.
     */
        public function store(Request $request)
    {
        $validated = $request->validate([
            'front_title' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'back_title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:Male,Female,L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:255',
            'niy' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:255',
            'no_ukg' => 'nullable|string|max:255',
            'nrg' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'last_education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'additional_position' => 'nullable|string|max:255',
            'task_start_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'appointment_date' => 'nullable|date',
            'last_sk_date' => 'nullable|date',
            'last_sk_number' => 'nullable|string|max:255',
            'work_period' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'zkteco_uid' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Leave,Inactive',
            'employee_type_code' => 'required|string|in:teacher,employee,management',
            'unit' => 'required|string|max:255',
        ]);

        $type = \App\Models\EmployeeType::where('code', $validated['employee_type_code'])->first();
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid employee type code.'
            ], 400);
        }

        $validated['employee_type_id'] = $type->id;
        unset($validated['employee_type_code']);

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($request->file('photo'));
            $image->scaleDown(width: 800);
            
            $filename = 'photos/' . uniqid() . '.webp';
            $fullPath = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $image->save($fullPath, 80);
            $validated['photo'] = $filename;
        }

        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'data' => $employee
        ], 201);
    }

    /**
     * Update the specified employee in the school unit.
     */
        public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $validated = $request->validate([
            'front_title' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'back_title' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:Male,Female,L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:255',
            'niy' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:255',
            'no_ukg' => 'nullable|string|max:255',
            'nrg' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'last_education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'additional_position' => 'nullable|string|max:255',
            'task_start_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'appointment_date' => 'nullable|date',
            'last_sk_date' => 'nullable|date',
            'last_sk_number' => 'nullable|string|max:255',
            'work_period' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'zkteco_uid' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Leave,Inactive',
            'employee_type_code' => 'required|string|in:teacher,employee,management',
            'unit' => 'required|string|max:255',
        ]);

        $type = \App\Models\EmployeeType::where('code', $validated['employee_type_code'])->first();
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid employee type code.'
            ], 400);
        }

        $validated['employee_type_id'] = $type->id;
        unset($validated['employee_type_code']);

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                $oldPath = str_contains($employee->photo, 'photos/') ? $employee->photo : 'photos/' . $employee->photo;
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($request->file('photo'));
            $image->scaleDown(width: 800);
            
            $filename = 'photos/' . uniqid() . '.webp';
            $fullPath = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $image->save($fullPath, 80);
            $validated['photo'] = $filename;
        }

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'data' => $employee
        ], 200);
    }

    /**
     * Remove the specified employee from the school unit.
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ]);
    }

    /**
     * Get all employee types in the database.
     */
    public function employeeTypes()
    {
        $types = \App\Models\EmployeeType::all(['id', 'code', 'name']);
        return response()->json($types);
    }

    /**
     * Get all leave types in the database.
     */
    public function leaveTypes()
    {
        $types = \App\Models\LeaveType::all(['id', 'name', 'code', 'status_code', 'gets_presence_bonus']);
        return response()->json($types);
    }

    /**
     * Sync working shifts from central HRD.
     */
    public function syncShifts(Request $request)
    {
        $shifts = $request->input('shifts', []);

        foreach ($shifts as $sData) {
            $shift = WorkingShift::updateOrCreate(
                ['code' => $sData['code']],
                [
                    'name' => $sData['name'],
                    'is_shift' => $sData['is_shift'],
                    'description' => $sData['description'] ?? null
                ]
            );

            // Sync shift details (clear old ones and insert new ones)
            $shift->details()->delete();
            if (isset($sData['details']) && is_array($sData['details'])) {
                foreach ($sData['details'] as $dData) {
                    $shift->details()->create([
                        'day_of_week' => $dData['day_of_week'],
                        'start_time' => $dData['start_time'] ?? null,
                        'end_time' => $dData['end_time'] ?? null,
                        'is_off' => $dData['is_off'] ?? false,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Shifts synced successfully.']);
    }

    /**
     * Sync employee shift schedules from central HRD.
     */
    public function syncSchedules(Request $request)
    {
        $schedules = $request->input('schedules', []);

        foreach ($schedules as $sData) {
            $employee = Employee::where('id', $sData['employee_id'])
                ->orWhere('nuptk_nip_nik', $sData['nuptk_nip_nik'] ?? null)
                ->first();

            if (!$employee) {
                continue;
            }

            $shift = WorkingShift::where('code', $sData['working_shift_code'])->first();
            if (!$shift) {
                continue;
            }

            EmployeeWorkingShift::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'working_shift_id' => $shift->id,
                    'start_date' => $sData['start_date'],
                ],
                [
                    'end_date' => $sData['end_date'] ?? null
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Schedules synced successfully.']);
    }

    /**
     * Sync holidays and adjustments from central HRD.
     */
    public function syncHolidays(Request $request)
    {
        $holidays = $request->input('holidays', []);

        foreach ($holidays as $hData) {
            $holiday = Holiday::updateOrCreate(
                ['original_date' => $hData['original_date']],
                [
                    'name' => $hData['name'],
                    'is_global' => $hData['is_global'] ?? true
                ]
            );

            // Sync adjustments (clear old and insert new)
            $holiday->adjustments()->delete();
            if (isset($hData['adjustments']) && is_array($hData['adjustments'])) {
                foreach ($hData['adjustments'] as $aData) {
                    $holiday->adjustments()->create([
                        'original_date' => $aData['original_date'],
                        'adjusted_date' => $aData['adjusted_date'],
                        'school_unit_id' => $aData['school_unit_id'] ?? null,
                        'reason' => $aData['reason'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Holidays synced successfully.']);
    }

    /**
     * Sync bonus schemas and tiers from central HRD.
     */
    public function syncBonusSchemas(Request $request)
    {
        $schemas = $request->input('schemas', []);

        foreach ($schemas as $sData) {
            $schema = BonusSchema::updateOrCreate(
                ['name' => $sData['name']],
                [
                    'is_active' => $sData['is_active'] ?? true
                ]
            );

            // Sync tiers (clear old and insert new)
            $schema->tiers()->delete();
            if (isset($sData['tiers']) && is_array($sData['tiers'])) {
                foreach ($sData['tiers'] as $tData) {
                    $schema->tiers()->create([
                        'tier_level' => $tData['tier_level'],
                        'nominal' => $tData['nominal'],
                        'max_late_minutes' => $tData['max_late_minutes'],
                        'max_absent_days' => $tData['max_absent_days'] ?? 0,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Bonus schemas synced successfully.']);
    }

    /**
     * Get all leave requests for HRD Central.
     */
    public function leaveRequests()
    {
        $requests = LeaveRequest::with(['employee', 'leaveType', 'processedBy'])->get()->map(function ($req) {
            return [
                'id' => $req->id,
                'employee_id' => $req->employee_id,
                'employee_name' => $req->employee ? $req->employee->name : null,
                'nuptk_nip_nik' => $req->employee ? $req->employee->nuptk_nip_nik : null,
                'type' => $req->leaveType ? $req->leaveType->name : $req->type,
                'status_code' => $req->leaveType ? $req->leaveType->status_code : ($req->type === 'Sakit' ? 'S' : ($req->type === 'Cuti' ? 'C' : ($req->type === 'Dinas' ? 'H' : 'I'))),
                'gets_presence_bonus' => $req->leaveType ? $req->leaveType->gets_presence_bonus : ($req->type === 'Dinas'),
                'start_date' => $req->start_date ? $req->start_date->format('Y-m-d') : null,
                'end_date' => $req->end_date ? $req->end_date->format('Y-m-d') : null,
                'reason' => $req->reason,
                'status' => $req->status,
                'notes' => $req->notes,
                'attachment' => $req->attachment ? asset('storage/' . $req->attachment) : null,
                'processed_by' => $req->processedBy ? ($req->processedBy->name . ' (' . (
                    [
                        'super_admin' => 'Super Admin',
                        'admin_paud' => 'Admin PAUD',
                        'admin_sd' => 'Admin SD',
                        'admin_smp' => 'Admin SMP',
                        'kepala_sekolah' => 'Kepala Sekolah',
                        'waka' => 'Wakil Kepala Sekolah',
                    ][$req->processedBy->role] ?? $req->processedBy->role
                ) . ')') : $req->processed_by_name,
            ];
        });

        return response()->json($requests);
    }

    /**
     * Receive approval decision from HRD Central.
     */
    public function leaveDecision(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|integer',
            'status' => 'required|string|in:Pending,Approved,Rejected',
            'notes' => 'nullable|string',
            'type' => 'nullable|string',
            'processed_by' => 'nullable|string',
        ]);

        $leave = LeaveRequest::with('leaveType')->find($request->input('leave_id'));
        if (!$leave) {
            return response()->json(['success' => false, 'message' => 'Leave request not found.'], 404);
        }

        $oldStatus = $leave->status;
        $leave->status = $request->input('status');
        $leave->notes = $request->input('notes');
        $leave->processed_by_id = null;
        $leave->processed_by_name = $request->input('processed_by');

        if ($request->filled('type')) {
            $newType = $request->input('type');
            $leave->type = $newType;
            
            // Sync leave_type_id
            $matchedLeaveType = \App\Models\LeaveType::where('name', $newType)->first();
            if ($matchedLeaveType) {
                $leave->leave_type_id = $matchedLeaveType->id;
            } else {
                $codeMap = ['Sakit' => 'S', 'Izin' => 'I', 'Cuti' => 'C', 'Dinas' => 'H'];
                if (isset($codeMap[$newType])) {
                    $matchedByCode = \App\Models\LeaveType::where('status_code', $codeMap[$newType])->first();
                    if ($matchedByCode) {
                        $leave->leave_type_id = $matchedByCode->id;
                    }
                }
            }
        }

        $leave->save();

        try {
            $employee = $leave->employee;
            if ($employee && $employee->user) {
                $employee->user->notify(new \App\Notifications\LeaveDecisionNotification($leave));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify employee for leave decision in unit: " . $e->getMessage());
        }

        // If changed from Approved to Pending/Rejected, remove/reset automatic attendance
        if ($oldStatus === 'Approved' && $leave->status !== 'Approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $attendance = Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();
                
                if ($attendance) {
                    if (is_null($attendance->clock_in) && is_null($attendance->clock_out)) {
                        $attendance->delete();
                    } else {
                        $attendance->update([
                            'status' => 'Present',
                            'calculated_bonus' => 0.00,
                        ]);
                    }
                }
            }
        }

        // If approved, update attendance table automatically for those days
        if ($leave->status === 'Approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $attendance = Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();

                $status = 'Leave';
                if ($leave->leaveType) {
                    if ($leave->leaveType->status_code === 'S') {
                        $status = 'Sick';
                    }
                } else {
                    if ($leave->type === 'Sakit') {
                        $status = 'Sick';
                    }
                }

                $getsBonus = $leave->leaveType ? $leave->leaveType->gets_presence_bonus : ($leave->type === 'Dinas');
                $calculatedBonus = 0.00;
                if ($getsBonus) {
                    $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                    if ($activeSchema) {
                        $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                            ->orderBy('nominal', 'desc')
                            ->first();
                        if ($maxTier) {
                            $calculatedBonus = $maxTier->nominal;
                        }
                    }
                }

                if ($attendance) {
                    $attendance->update([
                        'status' => $status,
                        'calculated_bonus' => $calculatedBonus,
                    ]);
                } else {
                    Attendance::create([
                        'employee_id' => $leave->employee_id,
                        'date' => $date->format('Y-m-d'),
                        'clock_in' => null,
                        'clock_out' => null,
                        'status' => $status,
                        'calculated_bonus' => $calculatedBonus,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Decision processed successfully.']);
    }

    /**
     * Sync announcements from central HRD.
     */
    public function syncAnnouncements(Request $request)
    {
        $action = $request->input('action', 'sync');
        $centralId = $request->input('central_id');

        if (!$centralId) {
            return response()->json(['success' => false, 'message' => 'Missing central_id.'], 400);
        }

        if ($action === 'delete') {
            \App\Models\Announcement::where('central_id', $centralId)->delete();
            return response()->json(['success' => true, 'message' => 'Announcement deleted successfully.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'target_audience' => 'required|string',
            'publish_date' => 'nullable|date_format:Y-m-d H:i:s',
            'expiry_date' => 'nullable|date_format:Y-m-d H:i:s',
            'is_active' => 'required|boolean',
            'attachment' => 'nullable|string',
        ]);

        // Find a default creator to satisfy foreign key (a superadmin or admin)
        $admin = \App\Models\User::whereIn('role', ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka'])->first()
            ?? \App\Models\User::first();
        
        $creatorId = $admin ? $admin->id : 1;

        $announcement = \App\Models\Announcement::updateOrCreate(
            ['central_id' => $centralId],
            [
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'target_audience' => $validated['target_audience'],
                'publish_date' => $validated['publish_date'] ?? now(),
                'expiry_date' => $validated['expiry_date'],
                'is_active' => $validated['is_active'],
                'attachment' => $validated['attachment'],
                'created_by' => $creatorId,
            ]
        );

        // If it's active, send notification locally
        if ($announcement->is_active) {
            $audiences = explode(',', $announcement->target_audience);
            $query = \App\Models\User::where('id', '!=', $creatorId);
            
            if (!in_array('global', $audiences)) {
                $query->where(function($q) use ($audiences) {
                    $q->whereHas('employee.employeeType', function($sq) use ($audiences) {
                        $sq->whereIn('code', $audiences);
                    })->orWhereIn('role', ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
                });
            }
            
            $users = $query->get();
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NewAnnouncementNotification($announcement));
        }

        return response()->json(['success' => true, 'message' => 'Announcement synced successfully.']);
    }

    /**
     * Sync payslip notification from central HRD.
     */
    public function syncPayslip(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $period = $request->input('period');
        $fileUrl = $request->input('file_url');

        if (!$employeeId || !$period || !$fileUrl) {
            return response()->json(['success' => false, 'message' => 'Missing required fields.'], 400);
        }

        // Find the user associated with this employee_id
        $user = \App\Models\User::where('employee_id', $employeeId)->first();

        if ($user) {
            $user->notify(new \App\Notifications\NewPayslipNotification($period, $fileUrl));
        }

        return response()->json(['success' => true, 'message' => 'Payslip notification sent successfully.']);
    }

    /**
     * Verify employee credentials for SSO.
     */
    public function verify(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (\Illuminate\Support\Facades\Auth::guard('web')->attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
            return response()->json([
                'success' => true,
                'role' => $user->role ?? 'teacher',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Password salah.'
        ], 401);
    }
}

