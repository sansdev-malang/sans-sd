<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status_code',
        'gets_presence_bonus',
        'requires_attendance',
        'requires_approval',
    ];

    protected $casts = [
        'gets_presence_bonus' => 'boolean',
        'requires_attendance' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    /**
     * Get the leave requests associated with this type.
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
