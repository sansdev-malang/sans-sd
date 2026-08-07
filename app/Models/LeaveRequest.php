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

    protected static function booted()
    {
        static::saved(function ($leave) {
            if ($leave->status === 'Approved') {
                $startDate = \Carbon\Carbon::parse($leave->start_date);
                $endDate = \Carbon\Carbon::parse($leave->end_date);
                
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $attendance = \App\Models\Attendance::where('employee_id', $leave->employee_id)
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
            }
        });
    }
}
