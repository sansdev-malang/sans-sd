<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicketSchedule extends Model
{
    protected $fillable = [
        'picket_area_id',
        'day_of_week',
        'employee_id',
    ];

    public function picketArea(): BelongsTo
    {
        return $this->belongsTo(PicketArea::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
