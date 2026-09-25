<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'class_level_id',
        'academic_year_id',
        'homeroom_teacher_id',
        'capacity',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'homeroom_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    protected $appends = [
        'full_name',
    ];

    /**
     * Get combined display name (e.g. "1A Berlian" or "1A").
     */
    public function getFullNameAttribute(): string
    {
        if ($this->code && $this->name) {
            if (str_starts_with(strtoupper($this->name), strtoupper($this->code))) {
                return $this->name;
            }
            return "{$this->code} {$this->name}";
        }
        return $this->name ?: ($this->code ?: '-');
    }

    /**
     * Get the active students count.
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->where('status', 'aktif')->count();
    }
}
