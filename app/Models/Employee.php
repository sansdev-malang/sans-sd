<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'front_title',
        'name',
        'back_title',
        'email',
        'employee_type_id',
        'unit',
        'gender',
        'birth_place',
        'birth_date',
        'nik',
        'niy',
        'nuptk',
        'no_ukg',
        'nrg',
        'pangkat_golongan',
        'last_education',
        'major',
        'position',
        'additional_position',
        'task_start_date',
        'appointment_date',
        'last_sk_date',
        'last_sk_number',
        'work_period',
        'address',
        'phone',
        'notes',
        'employment_status',
        'zkteco_uid',
        'photo',
        'status',
    ];

    /**
     * Get the employee type.
     */
        protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        $front = !empty($this->front_title) ? trim($this->front_title) . ' ' : '';
        $back = !empty($this->back_title) ? ', ' . trim($this->back_title) : '';
        return $front . $this->name . $back;
    }

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


