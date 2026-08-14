<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'notes',
        'attachment',
        'processed_by_id',
        'processed_by_name',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    public function syncToCentral()
    {
        try {
            $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
            if (!$hrdUrl) return;

            $schoolUnitCode = strtolower(config('app.school_unit', 'sd'));
            $schoolUnitId = [
                'paud' => 1,
                'sd' => 2,
                'smp' => 3,
            ][$schoolUnitCode] ?? 2;

            $statusCode = $this->leaveType ? $this->leaveType->status_code : ($this->type === 'Sakit' ? 'S' : ($this->type === 'Cuti' ? 'C' : ($this->type === 'Dinas' ? 'H' : 'I')));
            $getsPresenceBonus = $this->leaveType ? $this->leaveType->gets_presence_bonus : ($this->type === 'Dinas');
            $requiresAttendance = $this->leaveType ? $this->leaveType->requires_attendance : true;
            $requiresApproval = $this->leaveType ? $this->leaveType->requires_approval : true;

            $processedByName = $this->processedBy ? ($this->processedBy->name . ' (' . (
                [
                    'super_admin' => 'Super Admin',
                    'admin_paud' => 'Admin PAUD',
                    'admin_sd' => 'Admin SD',
                    'admin_smp' => 'Admin SMP',
                    'kepala_sekolah' => 'Kepala Sekolah',
                    'waka' => 'Wakil Kepala Sekolah',
                ][$this->processedBy->role] ?? $this->processedBy->role
            ) . ')') : $this->processed_by_name;

            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->post(rtrim($hrdUrl, '/') . '/api/sync/leave-request', [
                'school_unit_id' => $schoolUnitId,
                'remote_leave_id' => $this->id,
                'employee_id' => $this->employee_id,
                'employee_name' => $this->employee ? $this->employee->name : null,
                'type' => $this->leaveType ? $this->leaveType->name : $this->type,
                'status_code' => $statusCode,
                'gets_presence_bonus' => (bool)$getsPresenceBonus,
                'requires_attendance' => (bool)$requiresAttendance,
                'requires_approval' => (bool)$requiresApproval,
                'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
                'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
                'reason' => $this->reason,
                'status' => $this->status,
                'notes' => $this->notes,
                'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
                'processed_by' => $processedByName,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to sync leave request to central: " . $e->getMessage());
        }
    }

    public function deleteFromCentral()
    {
        try {
            $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
            if (!$hrdUrl) return;

            $schoolUnitCode = strtolower(config('app.school_unit', 'sd'));
            $schoolUnitId = [
                'paud' => 1,
                'sd' => 2,
                'smp' => 3,
            ][$schoolUnitCode] ?? 2;

            \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->post(rtrim($hrdUrl, '/') . '/api/sync/leave-request/delete', [
                'school_unit_id' => $schoolUnitId,
                'remote_leave_id' => $this->id,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete leave request from central: " . $e->getMessage());
        }
    }

    protected static function booted()
    {
        static::created(function ($leave) {
            try {
                $roles = ['super_admin', 'admin_sd', 'kepala_sekolah', 'waka'];
                $admins = \App\Models\User::whereIn('role', $roles)->get();
                foreach ($admins as $admin) {
                    if ($leave->employee && $admin->employee_id === $leave->employee_id) {
                        continue;
                    }
                    $admin->notify(new \App\Notifications\NewLeaveRequestNotification($leave));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify admins of new leave request: " . $e->getMessage());
            }
        });

        static::saved(function ($leave) {
            $leave->syncToCentral();

            $leaveType = $leave->leaveType ?? \App\Models\LeaveType::find($leave->leave_type_id);
            $requiresAttendance = $leaveType ? $leaveType->requires_attendance : true;

            if ($leave->status === 'Approved' && !$requiresAttendance) {
                $startDate = \Carbon\Carbon::parse($leave->start_date);
                $endDate = \Carbon\Carbon::parse($leave->end_date);

                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $attendance = \App\Models\Attendance::where('employee_id', $leave->employee_id)
                        ->where('date', $date->format('Y-m-d'))
                        ->first();

                    $status = 'Leave';
                    if ($leaveType) {
                        if ($leaveType->status_code === 'S') {
                            $status = 'Sick';
                        }
                    } else {
                        if ($leave->type === 'Sakit') {
                            $status = 'Sick';
                        }
                    }

                    $getsBonus = $leaveType ? $leaveType->gets_presence_bonus : ($leave->type === 'Dinas');
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
                        \App\Models\Attendance::create([
                            'employee_id' => $leave->employee_id,
                            'date' => $date->format('Y-m-d'),
                            'clock_in' => null,
                            'clock_out' => null,
                            'status' => $status,
                            'calculated_bonus' => $calculatedBonus,
                        ]);
                    }
                }
            } else {
                $startDate = \Carbon\Carbon::parse($leave->start_date);
                $endDate = \Carbon\Carbon::parse($leave->end_date);

                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $attendance = \App\Models\Attendance::where('employee_id', $leave->employee_id)
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
        });

        static::deleted(function ($leave) {
            $leave->deleteFromCentral();

            if ($leave->attachment) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($leave->attachment);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to delete leave attachment file: " . $e->getMessage());
                }
            }

            $startDate = \Carbon\Carbon::parse($leave->start_date);
            $endDate = \Carbon\Carbon::parse($leave->end_date);

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $attendance = \App\Models\Attendance::where('employee_id', $leave->employee_id)
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
        });
    }
}
