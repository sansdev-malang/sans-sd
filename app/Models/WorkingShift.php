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
        'description',
    ];

    protected $casts = [
        'is_shift' => 'boolean',
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
