<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_shift',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_shift' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(WorkingShiftDetail::class);
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeWorkingShift::class);
    }
}
