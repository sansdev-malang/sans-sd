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
     * Scope for active academic year.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
