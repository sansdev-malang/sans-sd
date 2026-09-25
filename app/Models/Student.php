<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        // Identitas & Legalitas
        'nis',
        'nisn',
        'nik',
        'no_kk',
        'birth_certificate_no',
        'citizenship',
        'spmb_candidate_id',
        'classroom_id',
        'academic_year_id',
        'full_name',
        'nickname',
        'gender',
        'student_type',
        'special_needs_type',
        'special_needs_notes',
        'birth_place',
        'birth_date',
        'religion',
        'student_photo_url',

        // Alamat & Domisili
        'address',
        'rt',
        'rw',
        'village',
        'district',
        'district_category',
        'city',
        'province',
        'postal_code',
        'residence_status',
        'distance_to_school',
        'home_phone',

        // Keluarga & Saudara
        'child_number',
        'siblings_count',
        'step_siblings_count',
        'adoptive_siblings_count',
        'home_language',

        // Kesehatan & Fisik
        'weight',
        'height',
        'blood_type',
        'severe_disease_history',
        'frequent_disease',

        // Data Ayah
        'father_name',
        'father_nik',
        'father_birth_place',
        'father_birth_date',
        'father_religion',
        'father_phone',
        'father_education',
        'father_job',
        'father_company',
        'father_company_address',
        'father_company_phone',
        'father_income',
        'father_email',

        // Data Ibu
        'mother_name',
        'mother_nik',
        'mother_birth_place',
        'mother_birth_date',
        'mother_religion',
        'mother_phone',
        'mother_education',
        'mother_job',
        'mother_company',
        'mother_company_address',
        'mother_company_phone',
        'mother_income',
        'mother_email',

        // Data Wali
        'guardian_name',
        'guardian_relation',
        'guardian_birth_place',
        'guardian_birth_date',
        'guardian_education',
        'guardian_job',
        'guardian_religion',
        'guardian_phone',
        'guardian_address',

        // Kontak Utama
        'parent_phone',
        'parent_email',

        // Asal Sekolah
        'previous_school',
        'origin_category',
        'previous_school_address',
        'sttb_number_date',

        // Dokumen & Status
        'documents',
        'checklist_documents',
        'status',
        'enrolled_date',
        'notes',
        'graduation_year',
        'diploma_number',
        'continued_school',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
        'guardian_birth_date' => 'date',
        'enrolled_date' => 'date',
        'documents' => 'array',
        'checklist_documents' => 'array',
        'child_number' => 'integer',
        'siblings_count' => 'integer',
        'step_siblings_count' => 'integer',
        'adoptive_siblings_count' => 'integer',
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

    public function classroomHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentClassroomHistory::class)->orderBy('academic_year_id', 'asc');
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
