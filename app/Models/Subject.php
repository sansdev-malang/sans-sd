<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit',
        'code',
        'name',
        'short_name',
        'group',
        'order',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relationship to homeroom and teaching assignments.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(HomeroomAssignment::class, 'subject_id');
    }

    /**
     * Scope active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Group label accessor.
     */
    public function getGroupLabelAttribute(): string
    {
        return $this->group ?? '-';
    }
}

