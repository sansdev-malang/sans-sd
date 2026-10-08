<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeroomAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'class_level_id',
        'classroom_id',
        'employee_id',
        'role',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Academic Year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relationship to Class Level.
     */
    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    /**
     * Relationship to Classroom.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relationship to Employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Alias for employee (teacher).
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Role display label helper.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'wali_kelas' => 'Wali Kelas',
            'guru_kelas' => 'Guru Kelas',
            'gpk' => 'GPK (Inklusi)',
            'gpq' => 'GPQ (Al-Qur\'an)',
            'koordinator_tingkat' => 'Koordinator Tingkat',
            'pendamping_tingkat' => 'Pendamping Tingkat',
            default => ucwords(str_replace('_', ' ', (string)$this->role)),
        };
    }
}
