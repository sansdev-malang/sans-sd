<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'nuptk_nip_nik',
        'employee_type_id',
        'unit',
        'subject_position',
        'gender',
        'employment_status',
        'zkteco_uid',
        'photo',
        'status',
    ];

    /**
     * Get the employee type.
     */
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the user account associated with the employee.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function workingShifts(): HasMany
    {
        return $this->hasMany(EmployeeWorkingShift::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
