<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'semester',
        'is_active',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get formatted semester (e.g. Ganjil, Genap).
     */
     public function getSemesterAttribute($value)
     {
         return ucfirst(strtolower($value ?? 'ganjil'));
     }
 
     /**
      * Set semester to lowercase in DB.
      */
     public function setSemesterAttribute($value)
     {
         $this->attributes['semester'] = strtolower($value ?? 'ganjil');
     }
 
    /**
     * Get human-friendly period label (e.g. "Juli – Desember 2026").
     */
    public function getPeriodLabelAttribute(): string
    {
        if ($this->start_date && $this->end_date) {
            $startMonth = $this->start_date->translatedFormat('F');
            $endMonth = $this->end_date->translatedFormat('F');
            $year = $this->end_date->format('Y');
            
            if ($this->start_date->format('Y') !== $year) {
                return "{$startMonth} " . $this->start_date->format('Y') . " – {$endMonth} {$year}";
            }
            return "{$startMonth} – {$endMonth} {$year}";
        }

        // Fallback based on name & semester
        if (preg_match('/(\d{4})[\/\-](\d{4})/', (string)$this->name, $matches)) {
            $y1 = $matches[1];
            $y2 = $matches[2];
        } elseif (preg_match('/(\d{4})/', (string)$this->name, $matches)) {
            $y1 = $matches[1];
            $y2 = (int)$y1 + 1;
        } else {
            $y1 = date('Y');
            $y2 = (int)$y1 + 1;
        }

        return strtolower($this->semester) === 'genap'
            ? "Januari – Juni {$y2}"
            : "Juli – Desember {$y1}";
    }

    /**
     * Scope for active academic year.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
