<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWorkingShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'working_shift_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
