<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nisn',
        'nik',
        'spmb_candidate_id',
        'classroom_id',
        'academic_year_id',
        'full_name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'city',
        'province',
        'previous_school',
        'student_photo_url',
        'father_name',
        'father_phone',
        'father_job',
        'mother_name',
        'mother_phone',
        'mother_job',
        'guardian_name',
        'guardian_phone',
        'parent_phone',
        'parent_email',
        'documents',
        'status',
        'enrolled_date',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrolled_date' => 'date',
        'documents' => 'array',
    ];

    protected $appends = [
        'formatted_gender',
        'age',
        'avatar_initials',
        'clean_parent_phone',
        'whatsapp_url',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function spmbCandidate(): BelongsTo
    {
        return $this->belongsTo(SpmbCandidate::class);
    }

    /**
     * Human-readable gender.
     */
    public function getFormattedGenderAttribute(): string
    {
        $g = strtoupper((string)$this->gender);
        if ($g === 'L' || $g === 'LAKI-LAKI' || $g === 'MALE') return 'Laki-laki';
        if ($g === 'P' || $g === 'PEREMPUAN' || $g === 'FEMALE') return 'Perempuan';
        return $this->gender ?: '-';
    }

    /**
     * Calculated age string.
     */
    public function getAgeAttribute(): ?string
    {
        if (!$this->birth_date) return null;
        $diff = \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now());
        return "{$diff->y} th " . ($diff->m > 0 ? "{$diff->m} bln" : "");
    }

    /**
     * Avatar initials.
     */
    public function getAvatarInitialsAttribute(): string
    {
        $names = explode(' ', trim($this->full_name));
        $initials = '';
        foreach (array_slice($names, 0, 2) as $n) {
            $initials .= mb_substr($n, 0, 1);
        }
        return strtoupper($initials ?: 'S');
    }

    /**
     * Clean phone number for WhatsApp.
     */
    public function getCleanParentPhoneAttribute(): ?string
    {
        $raw = $this->parent_phone ?: $this->father_phone ?: $this->mother_phone ?: $this->guardian_phone;
        if (!$raw) return null;

        $clean = preg_replace('/[^0-9]/', '', (string)$raw);
        if (str_starts_with($clean, '08')) {
            $clean = '628' . substr($clean, 2);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '628' . substr($clean, 1);
        }
        return $clean;
    }

    /**
     * Direct WhatsApp URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $phone = $this->clean_parent_phone;
        if (!$phone || strlen($phone) < 8) return null;
        $appName = function_exists('setting') ? setting('app_name', 'SD Anak Saleh') : 'SD Anak Saleh';
        $text = urlencode("Halo Ayah/Bunda {$this->full_name}, kami dari {$appName}.");
        return "https://wa.me/{$phone}?text={$text}";
    }
}
