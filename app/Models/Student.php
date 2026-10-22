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
        'gpk_employee_id',
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

        // Gelar & Status Wali
        'father_title_prefix',
        'father_title_suffix',
        'mother_title_prefix',
        'mother_title_suffix',
        'guardian_title_prefix',
        'guardian_title_suffix',
        'is_accompanied_by_guardian',

        // Fisik, Ciri & Etnis
        'ethnic_group',
        'skin_color',
        'hair_type',
        'hair_color',

        // Ortu Alamat & Media Sosial
        'father_address',
        'father_social_media',
        'mother_address',
        'mother_social_media',

        // Wali Detail
        'guardian_nik',
        'guardian_company',
        'guardian_company_address',
        'guardian_company_phone',
        'guardian_income',
        'guardian_email',
        'guardian_social_media',

        // Domisili & Transportasi Tambahan
        'dusun',
        'living_with',
        'transportation',
        'travel_time_minutes',

        // Kontak Darurat
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',

        // Cita-cita, Hobi, Harapan & Catatan
        'student_hobby',
        'student_aspiration',
        'parent_aspiration',
        'planned_school_after',
        'parent_message',
        'parent_suggestion',

        // Hybrid JSON Fields
        'family_data',
        'health_habits',
        'survey_answers',
        'extra_fields',

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
        'family_data' => 'array',
        'health_habits' => 'array',
        'survey_answers' => 'array',
        'extra_fields' => 'array',
        'is_accompanied_by_guardian' => 'boolean',
        'travel_time_minutes' => 'integer',
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

    public function gpkTeacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'gpk_employee_id');
    }

    public function classroomHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentClassroomHistory::class)->orderBy('academic_year_id', 'asc');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentDocument::class)->orderBy('created_at', 'desc');
    }

    public function uploadedDocuments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentDocument::class)->orderBy('created_at', 'desc');
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentAttendance::class)->orderBy('date', 'desc');
    }

    /**
     * Unified document completeness checklist aggregated from StudentDocuments, Student.documents, and SPMB.
     */
    public function getDocumentChecklistAttribute(): array
    {
        $akta = null;
        $kk = null;
        $foto = $this->student_photo_url ?: null;
        $ktp = null;
        $kia = null;
        $otherList = [];

        // 1. Check from uploadedDocuments relation
        $docs = $this->relationLoaded('uploadedDocuments') 
            ? $this->uploadedDocuments 
            : ($this->relationLoaded('documents') ? $this->documents : $this->uploadedDocuments()->get());

        foreach ($docs as $d) {
            $type = strtolower($d->document_type);
            $url = $d->file_url;
            if (str_contains($type, 'akta')) {
                $akta = ['url' => $url, 'title' => $d->title, 'source' => 'Arsip Siswa', 'id' => $d->id];
            } elseif (str_contains($type, 'kk') || str_contains($type, 'kartu_keluarga')) {
                $kk = ['url' => $url, 'title' => $d->title, 'source' => 'Arsip Siswa', 'id' => $d->id];
            } elseif (str_contains($type, 'ktp')) {
                $ktp = ['url' => $url, 'title' => $d->title, 'source' => 'Arsip Siswa', 'id' => $d->id];
            } elseif (str_contains($type, 'foto')) {
                if (!$foto) $foto = $url;
            } elseif (str_contains($type, 'kia') || str_contains($type, 'imunisasi')) {
                $kia = ['url' => $url, 'title' => $d->title, 'source' => 'Arsip Siswa', 'id' => $d->id];
            } else {
                $otherList[] = ['url' => $url, 'title' => $d->title, 'type' => $d->type_label, 'id' => $d->id];
            }
        }

        // 2. Check from JSON documents array if not already found
        if (is_array($this->documents)) {
            foreach ($this->documents as $k => $doc) {
                $url = is_array($doc) ? ($doc['url'] ?? $doc['file_url'] ?? '') : (is_string($doc) ? $doc : '');
                if (!$url) continue;
                $key = strtolower((string)$k);
                if (!$akta && (str_contains($key, 'akta') || str_contains($key, 'birth'))) {
                    $akta = ['url' => $url, 'title' => 'Akta Kelahiran', 'source' => 'Data Siswa'];
                } elseif (!$kk && (str_contains($key, 'kk') || str_contains($key, 'family'))) {
                    $kk = ['url' => $url, 'title' => 'Kartu Keluarga', 'source' => 'Data Siswa'];
                } elseif (!$ktp && str_contains($key, 'ktp')) {
                    $ktp = ['url' => $url, 'title' => 'KTP Orang Tua', 'source' => 'Data Siswa'];
                } elseif (!$kia && (str_contains($key, 'kia') || str_contains($key, 'imunisasi'))) {
                    $kia = ['url' => $url, 'title' => 'Buku KIA', 'source' => 'Data Siswa'];
                }
            }
        }

        // 3. Check SPMB candidate relation if linked
        if ($this->spmb_candidate_id && $this->relationLoaded('spmbCandidate') && $this->spmbCandidate) {
            $cand = $this->spmbCandidate;
            if (!$foto && !empty($cand->photo_url)) $foto = $cand->photo_url;
            if (!$akta && !empty($cand->birth_certificate_url)) {
                $akta = ['url' => $cand->birth_certificate_url, 'title' => 'Akta Kelahiran (SPMB)', 'source' => 'SPMB'];
            }
            if (!$kk && !empty($cand->family_card_url)) {
                $kk = ['url' => $cand->family_card_url, 'title' => 'Kartu Keluarga (SPMB)', 'source' => 'SPMB'];
            }
        }

        $coreCount = ($akta ? 1 : 0) + ($kk ? 1 : 0) + ($foto ? 1 : 0);
        $totalCount = $coreCount + ($ktp ? 1 : 0) + ($kia ? 1 : 0) + count($otherList);

        return [
            'akta' => $akta,
            'kk' => $kk,
            'foto' => $foto,
            'ktp' => $ktp,
            'kia' => $kia,
            'others' => $otherList,
            'other_docs' => $otherList,
            'is_complete' => ($akta !== null && $kk !== null && $foto !== null),
            'core_count' => $coreCount,
            'total_count' => $totalCount,
        ];
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
        $unitName = function_exists('setting') ? setting('unit_name', 'SD Anak Saleh') : 'SD Anak Saleh';
        $text = urlencode("Halo Ayah/Bunda {$this->full_name}, kami dari {$unitName}.");
        return "https://wa.me/{$phone}?text={$text}";
    }

    protected static function booted()
    {
        static::saving(function (Student $student) {
            $student->normalizeDapodikAttributes();
        });
    }

    /**
     * Standardize student attributes based on official Dapodik / National School conventions.
     */
    public function normalizeDapodikAttributes(): void
    {
        // 1. UPPERCASE Fields (Nama Lengkap Siswa, Dokumen Resmi, Tipe, Golongan Darah)
        $this->full_name = self::formatUppercase($this->full_name);
        
        if (!empty($this->student_type)) {
            $st = strtoupper(trim((string)$this->student_type));
            if (str_contains($st, 'PDBK') || str_contains($st, 'MBK') || str_contains($st, 'ABK') || str_contains($st, 'KHUSUS') || str_contains($st, 'INKLUSI')) {
                $this->student_type = 'PDBK';
            } else {
                $this->student_type = 'REGULER';
            }
        }
        
        $this->special_needs_type = self::formatUppercase($this->special_needs_type);
        $this->blood_type = self::formatUppercase($this->blood_type);
        
        // Gender normalization
        if (!empty($this->gender)) {
            $g = strtoupper(trim((string)$this->gender));
            if (in_array($g, ['L', 'LAKI-LAKI', 'MALE', 'PRIA'])) {
                $this->gender = 'L';
            } elseif (in_array($g, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA'])) {
                $this->gender = 'P';
            }
        }
        
        // Alphanumeric Codes
        $this->nis = self::formatCleanCode($this->nis);
        $this->nisn = self::formatCleanCode($this->nisn);
        $this->nik = self::formatCleanCode($this->nik);
        $this->no_kk = self::formatCleanCode($this->no_kk);
        $this->birth_certificate_no = self::formatCleanCode($this->birth_certificate_no);
        $this->father_nik = self::formatCleanCode($this->father_nik);
        $this->mother_nik = self::formatCleanCode($this->mother_nik);
        $this->postal_code = self::formatCleanCode($this->postal_code);
        $this->diploma_number = self::formatCleanCode($this->diploma_number);

        // 2. Title Case Fields (Nama Orang Tua, Alamat, Wilayah, Agama, dsb.)
        $this->nickname = self::formatIndonesianTitleCase($this->nickname);
        $this->birth_place = self::formatIndonesianTitleCase($this->birth_place);
        $this->religion = self::formatIndonesianTitleCase($this->religion);
        $this->citizenship = self::formatIndonesianTitleCase($this->citizenship);
        
        $this->address = self::formatIndonesianTitleCase($this->address);
        $this->village = self::formatIndonesianTitleCase($this->village);
        $this->district = self::formatIndonesianTitleCase($this->district);
        $this->district_category = self::formatIndonesianTitleCase($this->district_category);
        $this->city = self::formatIndonesianTitleCase($this->city);
        $this->province = self::formatIndonesianTitleCase($this->province);
        $this->residence_status = self::formatIndonesianTitleCase($this->residence_status);
        $this->home_language = self::formatIndonesianTitleCase($this->home_language);

        // Data Ayah
        $this->father_name = self::formatIndonesianTitleCase($this->father_name);
        $this->father_birth_place = self::formatIndonesianTitleCase($this->father_birth_place);
        $this->father_religion = self::formatIndonesianTitleCase($this->father_religion);
        $this->father_education = self::formatIndonesianTitleCase($this->father_education);
        $this->father_job = self::formatIndonesianTitleCase($this->father_job);
        $this->father_company = self::formatIndonesianTitleCase($this->father_company);
        $this->father_company_address = self::formatIndonesianTitleCase($this->father_company_address);

        // Data Ibu
        $this->mother_name = self::formatIndonesianTitleCase($this->mother_name);
        $this->mother_birth_place = self::formatIndonesianTitleCase($this->mother_birth_place);
        $this->mother_religion = self::formatIndonesianTitleCase($this->mother_religion);
        $this->mother_education = self::formatIndonesianTitleCase($this->mother_education);
        $this->mother_job = self::formatIndonesianTitleCase($this->mother_job);
        $this->mother_company = self::formatIndonesianTitleCase($this->mother_company);
        $this->mother_company_address = self::formatIndonesianTitleCase($this->mother_company_address);

        // Data Wali
        $this->guardian_name = self::formatIndonesianTitleCase($this->guardian_name);
        $this->guardian_relation = self::formatIndonesianTitleCase($this->guardian_relation);
        $this->guardian_birth_place = self::formatIndonesianTitleCase($this->guardian_birth_place);
        $this->guardian_religion = self::formatIndonesianTitleCase($this->guardian_religion);
        $this->guardian_education = self::formatIndonesianTitleCase($this->guardian_education);
        $this->guardian_job = self::formatIndonesianTitleCase($this->guardian_job);
        $this->guardian_address = self::formatIndonesianTitleCase($this->guardian_address);

        // Asal Sekolah & Lanjutan
        $this->previous_school = self::formatIndonesianTitleCase($this->previous_school);
        $this->previous_school_address = self::formatIndonesianTitleCase($this->previous_school_address);
        $this->continued_school = self::formatIndonesianTitleCase($this->continued_school);

        // 3. Lowercase Emails
        $this->parent_email = self::formatLowercase($this->parent_email);
        $this->father_email = self::formatLowercase($this->father_email);
        $this->mother_email = self::formatLowercase($this->mother_email);
    }

    public static function formatUppercase(?string $string): ?string
    {
        if ($string === null) return null;
        $string = trim(preg_replace('/\s+/', ' ', $string));
        return $string !== '' ? mb_strtoupper($string, 'UTF-8') : null;
    }

    public static function formatLowercase(?string $string): ?string
    {
        if ($string === null) return null;
        $string = trim($string);
        return $string !== '' ? mb_strtolower($string, 'UTF-8') : null;
    }

    public static function formatCleanCode(?string $string): ?string
    {
        if ($string === null) return null;
        $string = trim(preg_replace('/\s+/', '', $string));
        return $string !== '' ? strtoupper($string) : null;
    }

    public static function formatIndonesianTitleCase(?string $string): ?string
    {
        if ($string === null) return null;
        $string = trim(preg_replace('/\s+/', ' ', $string));
        if ($string === '') return null;

        $words = explode(' ', $string);
        $result = [];

        $specialTerms = [
            'dr.' => 'Dr.', 'drh.' => 'Drh.', 'dr' => 'Dr.', 'drh' => 'Drh.',
            'ir.' => 'Ir.', 'ir' => 'Ir.', 'prof.' => 'Prof.', 'prof' => 'Prof.',
            'h.' => 'H.', 'hj.' => 'Hj.', 's.t.' => 'S.T.', 's.t' => 'S.T.',
            's.pd.' => 'S.Pd.', 's.pd' => 'S.Pd.', 'm.pd.' => 'M.Pd.', 'm.pd' => 'M.Pd.',
            's.kom.' => 'S.Kom.', 's.kom' => 'S.Kom.', 's.si.' => 'S.Si.', 's.si' => 'S.Si.',
            's.ap.' => 'S.Ap.', 's.ap' => 'S.Ap.', 's.e.' => 'S.E.', 's.e' => 'S.E.',
            'se.' => 'S.E.', 'se' => 'S.E.', 's.sos.' => 'S.Sos.', 's.sos' => 'S.Sos.',
            's.h.' => 'S.H.', 's.h' => 'S.H.', 's.psi.' => 'S.Psi.', 's.psi' => 'S.Psi.',
            's.ked.' => 'S.Ked.', 's.ked' => 'S.Ked.', 's.ag.' => 'S.Ag.', 's.ag' => 'S.Ag.',
            'm.m.' => 'M.M.', 'm.m' => 'M.M.', 'm.si.' => 'M.Si.', 'm.si' => 'M.Si.',
            'a.md.' => 'A.Md.', 'a.md' => 'A.Md.', 'amd.' => 'A.Md.', 'amd' => 'A.Md.',
            'apt.' => 'Apt.', 'apt' => 'Apt.', 'l.c.' => 'Lc.', 'lc.' => 'Lc.', 'lc' => 'Lc.',
            'rt' => 'RT', 'rw' => 'RW', 'rt.' => 'RT', 'rw.' => 'RW',
            'no.' => 'No.', 'no' => 'No.', 'jl.' => 'Jl.', 'jl' => 'Jl.',
            'wni' => 'WNI', 'wna' => 'WNA', 'sd' => 'SD', 'sdi' => 'SDI',
            'smp' => 'SMP', 'sma' => 'SMA', 'smk' => 'SMK', 'tk' => 'TK',
            'tpa' => 'TPA', 'kb' => 'KB', 'ra' => 'RA', 'mi' => 'MI',
            'mts' => 'MTs', 'ma' => 'MA', 'pt' => 'PT', 'cv' => 'CV',
        ];

        $romanNumerals = ['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'];
        $prepositions = ['dan', 'di', 'ke', 'dari', 'yang', 'pada', 'untuk', 'bin', 'binti'];

        foreach ($words as $w) {
            $lower = strtolower($w);
            $cleanLower = rtrim(ltrim($lower, '(,'), '),.');

            if (isset($specialTerms[$lower])) {
                $formatted = $specialTerms[$lower];
                if (str_ends_with($w, ',')) $formatted .= ',';
                if (str_starts_with($w, '(')) $formatted = '(' . $formatted;
                if (str_ends_with($w, ')')) $formatted .= ')';
                $result[] = $formatted;
            } elseif (isset($specialTerms[$cleanLower])) {
                $formatted = $specialTerms[$cleanLower];
                if (str_ends_with($w, ',')) $formatted .= ',';
                if (str_starts_with($w, '(')) $formatted = '(' . $formatted;
                if (str_ends_with($w, ')')) $formatted .= ')';
                $result[] = $formatted;
            } elseif (in_array($lower, $romanNumerals)) {
                $result[] = strtoupper($w);
            } elseif (in_array($lower, $prepositions) && count($result) > 0) {
                $result[] = $lower;
            } else {
                $result[] = mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
            }
        }

        return implode(' ', $result);
    }
}

