<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingShiftDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'working_shift_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_off',
    ];

    protected $casts = [
        'is_off' => 'boolean',
    ];

    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
