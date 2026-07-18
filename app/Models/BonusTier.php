<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_schema_id',
        'tier_level',
        'nominal',
        'max_late_minutes',
        'max_absent_days',
    ];

    public function bonusSchema()
    {
        return $this->belongsTo(BonusSchema::class);
    }
}
