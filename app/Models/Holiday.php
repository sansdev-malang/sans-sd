<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_date',
        'is_global',
    ];

    protected $casts = [
        'original_date' => 'date',
        'is_global' => 'boolean',
    ];

    public function adjustments()
    {
        return $this->hasMany(HolidayAdjustment::class);
    }
}
