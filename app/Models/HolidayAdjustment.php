<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_id',
        'original_date',
        'adjusted_date',
        'school_unit_id',
        'reason',
    ];

    protected $casts = [
        'original_date' => 'date',
        'adjusted_date' => 'date',
    ];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }
}
