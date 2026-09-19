<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PicketArea extends Model
{
    protected $fillable = [
        'name',
        'jobs',
        'start_time',
        'end_time',
        'duty_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->start_time && $model->end_time) {
                $startFormatted = substr($model->start_time, 0, 5);
                $endFormatted = substr($model->end_time, 0, 5);
                $model->duty_hours = "{$startFormatted} - {$endFormatted}";
            }
        });
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PicketSchedule::class);
    }
}
