<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbCandidate extends Model
{
    use HasFactory;

    protected $table = 'spmb_candidates';

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
        'spmb_verified_at' => 'datetime',
        'spmb_registered_at' => 'datetime',
        'activated_at' => 'datetime',
        'enrolled_at' => 'datetime',
        'is_active_student' => 'boolean',
        'is_enrolled' => 'boolean',
        'documents' => 'array',
        'payments_data' => 'array',
        'raw_payload' => 'array',
    ];

    protected $appends = [
        'whatsapp_url', 
        'initials', 
        'formatted_birth_date',
        'age_string',
        'student_photo_url',
        'formatted_documents',
        'formatted_address',
        'formatted_payments',
        'fee_categories',
        'payment_summary',
        'referral',
        'has_payment_access',
        'category',
        'admission_level',
        'class_program',
        'registration_type',
        'jenjang_code',
        'jenjang_name',
        'all_jenjang_codes',
        'services_list',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Mutator & Accessor untuk normalisasi format Tahun Ajaran (misal 2026-2027 -> 2026/2027)
     */
    public function getAcademicYearAttribute($value): ?string
    {
        return $value ? str_replace('-', '/', $value) : null;
    }

    public function setAcademicYearAttribute($value): void
    {
        $this->attributes['academic_year'] = $value ? str_replace('-', '/', trim($value)) : null;
    }

    /**
     * Getter & Setter untuk fleksibilitas alias kolom status
     */
    public function getRegistrationStatusAttribute(): string
    {
        return $this->attributes['spmb_status'] ?? 'verified';
    }

    public function setRegistrationStatusAttribute($value): void
    {
        $this->attributes['spmb_status'] = $value;
    }

    public function getPaymentStatusAttribute(): string
    {
        return $this->attributes['spmb_payment_status'] ?? 'unpaid';
    }

    public function setPaymentStatusAttribute($value): void
    {
        $this->attributes['spmb_payment_status'] = $value;
    }

    public function getPaymentsAttribute(): ?array
    {
        return $this->payments_data ?? ($this->raw_payload['payments'] ?? []);
    }

    /**
     * URL Foto Calon Siswa
     */
    public function getStudentPhotoUrlAttribute(): ?string
    {
        if (is_array($this->documents)) {
            foreach ($this->documents as $doc) {
                if (is_array($doc)) {
                    $key = $doc['key'] ?? '';
                    $name = strtolower($doc['name'] ?? '');
                    if (in_array($key, ['student_photo_path', 'student_photo', 'photo']) || str_contains($name, 'pas foto') || str_contains($name, 'foto')) {
                        if (!empty($doc['url'])) return $doc['url'];
                    }
                }
            }
            return $this->documents['student_photo'] 
                ?? $this->documents['student_photo_path'] 
                ?? $this->documents['photo']
                ?? null;
        }

        return $this->raw_payload['student_bio']['photo_url'] ?? null;
    }

    /**
     * Format Dokumen Dinamis & Terstruktur
     */
    public function getFormattedDocumentsAttribute(): array
    {
        if (!is_array($this->documents) || empty($this->documents)) {
            return [];
        }

        $list = [];
        $labelMap = [
            'student_photo' => 'Pas Foto Calon Murid (Foto Formal)',
            'student_photo_path' => 'Pas Foto Calon Murid (Foto Formal)',
            'birth_certificate' => 'Akta Kelahiran',
            'birth_certificate_path' => 'Akta Kelahiran',
            'family_card' => 'Kartu Keluarga (KK)',
            'family_card_path' => 'Kartu Keluarga (KK)',
            'diploma_certificate' => 'Ijazah / Surat Keterangan Aktif TK',
            'diploma_certificate_path' => 'Ijazah / Surat Keterangan Aktif TK',
            'student_card' => 'NISN / KIA / Kartu Pelajar (Opsional)',
            'student_card_path' => 'NISN / KIA / Kartu Pelajar (Opsional)',
            'special_needs_assessment_path' => 'Asesmen Kebutuhan Khusus / Psikotes (Jika Ada)',
            'payment_receipt_path' => 'Bukti Pembayaran Pendaftaran',
        ];

        foreach ($this->documents as $k => $v) {
            if (is_array($v)) {
                $list[] = [
                    'key' => $v['key'] ?? (is_string($k) ? $k : 'doc_' . count($list)),
                    'name' => $v['name'] ?? ($v['label'] ?? ($labelMap[$v['key'] ?? ''] ?? 'Berkas Dokumen')),
                    'url' => $v['url'] ?? '#',
                ];
            } elseif (is_string($v) && !empty($v)) {
                $label = $labelMap[$k] ?? ucwords(str_replace(['_', '-'], ' ', $k));
                $list[] = [
                    'key' => $k,
                    'name' => $label,
                    'url' => $v,
                ];
            }
        }

        return $list;
    }

    /**
     * Format Tanggal Lahir Bahasa Indonesia
     */
    public function getFormattedBirthDateAttribute(): ?string
    {
        if (!$this->birth_date) return null;
        return $this->birth_date->translatedFormat('d F Y');
    }

    /**
     * Usia Calon Siswa
     */
    public function getAgeStringAttribute(): ?string
    {
        if (!$this->birth_date) return null;
        $diff = \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now());
        return "{$diff->y} th " . ($diff->m > 0 ? "{$diff->m} bln" : "");
    }

    /**
     * Alamat Lengkap Terstruktur
     */
    public function getFormattedAddressAttribute(): string
    {
        $bioAddr = $this->raw_payload['student_bio']['address'] ?? [];
        if (is_array($bioAddr) && !empty($bioAddr)) {
            $parts = [];
            if (!empty($bioAddr['street'])) $parts[] = $bioAddr['street'];
            $rtRw = '';
            if (!empty($bioAddr['rt'])) $rtRw .= 'RT ' . $bioAddr['rt'];
            if (!empty($bioAddr['rw'])) $rtRw .= ($rtRw ? ' / ' : '') . 'RW ' . $bioAddr['rw'];
            if ($rtRw) $parts[] = $rtRw;
            if (!empty($bioAddr['village'])) $parts[] = 'Kel. ' . $bioAddr['village'];
            if (!empty($bioAddr['district'])) $parts[] = 'Kec. ' . $bioAddr['district'];
            if (!empty($bioAddr['city'])) $parts[] = $bioAddr['city'];
            if (!empty($bioAddr['province'])) $parts[] = $bioAddr['province'];
            if (!empty($bioAddr['postal_code'])) $parts[] = 'Kode Pos: ' . $bioAddr['postal_code'];
            if (!empty($parts)) {
                return implode(', ', $parts);
            }
            if (!empty($bioAddr['full_address'])) {
                return $bioAddr['full_address'];
            }
        }

        return $this->address ?: '-';
    }

    /**
     * Kategori Murid (Reguler / PDBK Inklusi)
     */
    public function getCategoryAttribute(): string
    {
        if (!empty($this->student_type)) {
            return $this->student_type;
        }
        $target = strtoupper($this->target_class ?? '');
        if (str_contains($target, 'MBK') || str_contains($target, 'PDBK') || str_contains($target, 'INKLUSI') || $this->special_needs_type) {
            return 'PDBK';
        }
        return 'REGULER';
    }

    /**
     * Jenjang / Level Masuk (Kelas 1 - Kelas 6)
     */
    public function getAdmissionLevelAttribute(): string
    {
        return $this->target_class ?: ($this->raw_payload['admission_level'] ?? ($this->raw_payload['target_class'] ?? 'Kelas 1'));
    }

    /**
     * Program / Kategori Murid (Reguler / PDBK)
     */
    public function getClassProgramAttribute(): string
    {
        return $this->student_type ?: ($this->raw_payload['class_program'] ?? ($this->raw_payload['student_type'] ?? 'Reguler'));
    }

    /**
     * Jalur Pendaftaran
     */
    public function getRegistrationTypeAttribute(): string
    {
        return $this->raw_payload['registration_type'] ?? ($this->raw_payload['entry_type'] ?? ($this->raw_payload['admission_type'] ?? 'Murid Baru'));
    }

    /**
     * Kode Jenjang Singkat (SD)
     */
    public function getJenjangCodeAttribute(): string
    {
        return 'SD';
    }

    /**
     * Nama Lengkap Jenjang (Sekolah Dasar)
     */
    public function getJenjangNameAttribute(): string
    {
        return 'Sekolah Dasar (SD)';
    }

    /**
     * Seluruh Kode Jenjang
     */
    public function getAllJenjangCodesAttribute(): array
    {
        return ['SD'];
    }

    /**
     * Layanan Tambahan
     */
    public function getServicesListAttribute(): array
    {
        $rawServices = $this->raw_payload['extra_services'] 
            ?? ($this->raw_payload['services'] 
            ?? ($this->raw_payload['additional_services'] ?? []));

        if (is_array($rawServices) && !empty($rawServices)) {
            return array_map(function($s) {
                return is_array($s) ? ($s['name'] ?? 'Layanan') : (string)$s;
            }, $rawServices);
        }

        return [];
    }

    /**
     * Riwayat Pembayaran Terstruktur
     */
    public function getFormattedPaymentsAttribute(): array
    {
        $rawPayments = $this->payments_data ?? ($this->raw_payload['payments'] ?? []);
        if (!is_array($rawPayments) || empty($rawPayments)) {
            return [];
        }

        $list = [];
        foreach ($rawPayments as $p) {
            if (!is_array($p)) continue;
            $amount = (float)($p['amount'] ?? 0);
            $paidAt = !empty($p['paid_at']) ? \Carbon\Carbon::parse($p['paid_at'])->translatedFormat('d M Y, H:i') : null;
            $status = strtolower($p['status'] ?? 'pending');
            $isPaid = in_array($status, ['paid', 'lunas', 'settlement', 'success']);

            $list[] = [
                'invoice_number' => $p['invoice_number'] ?? '-',
                'payment_type' => $p['payment_type'] ?? 'Pendaftaran SPMB',
                'amount' => $amount,
                'formatted_amount' => 'Rp ' . number_format($amount, 0, ',', '.'),
                'payment_method' => $p['payment_method'] ?? ($p['payment_channel'] ?? 'Online Payment'),
                'status' => $status,
                'is_paid' => $isPaid,
                'paid_at' => $paidAt,
            ];
        }

        return $list;
    }

    public function getFeeCategoriesAttribute(): array
    {
        return $this->raw_payload['fee_categories'] ?? [];
    }

    public function getPaymentSummaryAttribute(): ?array
    {
        return $this->raw_payload['payment_summary'] ?? null;
    }

    public function getReferralAttribute(): ?array
    {
        return $this->raw_payload['referral'] ?? null;
    }

    public function getHasPaymentAccessAttribute(): bool
    {
        return !empty($this->payment_status)
            || (!empty($this->payments_data) && is_array($this->payments_data) && count($this->payments_data) > 0)
            || (!empty($this->raw_payload['fee_categories']) && is_array($this->raw_payload['fee_categories']) && count($this->raw_payload['fee_categories']) > 0);
    }

    /**
     * Bersihkan nomor telepon untuk WhatsApp
     */
    public function getCleanPhone(): ?string
    {
        $phone = $this->parent_phone ?? $this->father_phone ?? $this->mother_phone;
        if (!$phone) return null;

        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Direct WhatsApp URL
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $cleanPhone = $this->getCleanPhone();
        if (!$cleanPhone) return null;

        $unitName = function_exists('setting') ? setting('unit_name', 'SD Anak Saleh Malang') : 'SD Anak Saleh Malang';
        $message = urlencode("Assalamu'alaikum Wr. Wb. Bapak/Ibu wali dari ananda *{$this->full_name}* (No. Registrasi: {$this->registration_number}). Terima kasih atas pendaftarannya di {$unitName}.");
        return "https://wa.me/{$cleanPhone}?text={$message}";
    }

    /**
     * Inisial Nama untuk Avatar Fallback
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->full_name ?? ''));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->full_name ?? 'SD', 0, 2));
    }

    /**
     * Upsert a candidate record from incoming SPMB JSON payload.
     */
    public static function syncFromPayload(array $payload): self
    {
        $regNumber = $payload['registration_number'] 
            ?? ($payload['registration_no'] 
            ?? ($payload['id_label'] 
            ?? (!empty($payload['id']) ? ('SPMB-' . str_pad($payload['id'], 5, '0', STR_PAD_LEFT)) : null)));

        if (!$regNumber) {
            throw new \InvalidArgumentException('Registration number is missing in SPMB payload.');
        }

        $bio = $payload['student_bio'] ?? ($payload['candidate_bio'] ?? []);
        $address = $bio['address'] ?? ($payload['address'] ?? []);
        $parents = $payload['parent_info'] ?? ($payload['parents'] ?? []);
        $father = $parents['father'] ?? [];
        $mother = $parents['mother'] ?? [];
        $guardian = $parents['guardian'] ?? ($payload['guardian'] ?? []);
        $contact = $parents['primary_contact'] ?? ($payload['primary_contact'] ?? []);
        $schoolOrigin = $payload['school_origin'] ?? [];
        $documents = $payload['documents'] ?? [];
        $payments = $payload['payments'] ?? [];
        $unit = $payload['unit'] ?? [];

        // Gender formatting
        $gender = $bio['gender'] ?? ($payload['gender'] ?? null);
        if ($gender) {
            $genderLower = strtolower($gender);
            if (str_contains($genderLower, 'laki') || $genderLower === 'male' || $genderLower === 'l') {
                $gender = 'male';
            } elseif (str_contains($genderLower, 'perempuan') || $genderLower === 'female' || $genderLower === 'p') {
                $gender = 'female';
            }
        }

        // Birth Date
        $birthDate = null;
        if (!empty($bio['birth_date'])) {
            try {
                $birthDate = \Carbon\Carbon::parse($bio['birth_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $birthDate = null;
            }
        }

        $verifiedAt = null;
        if (!empty($payload['verified_at'])) {
            try {
                $verifiedAt = \Carbon\Carbon::parse($payload['verified_at']);
            } catch (\Exception $e) {
                $verifiedAt = null;
            }
        }

        $registeredAt = null;
        if (!empty($payload['created_at'])) {
            try {
                $registeredAt = \Carbon\Carbon::parse($payload['created_at']);
            } catch (\Exception $e) {
                $registeredAt = null;
            }
        }

        $parentPhone = $contact['whatsapp'] ?? ($parents['primary_whatsapp'] ?? ($father['phone'] ?? ($mother['phone'] ?? ($guardian['phone'] ?? ($payload['parent_phone'] ?? null)))));
        $fullAddress = is_string($address) ? $address : ($address['full_address'] ?? ($address['street'] ?? ($address['street_address'] ?? null)));

        // Student Type (MBK / ABK / Inklusi -> PDBK)
        $rawStudentType = $payload['student_type'] ?? ($payload['type'] ?? ($payload['applicant_type'] ?? ($bio['student_type'] ?? ($payload['class_program'] ?? 'REGULER'))));
        $stUpper = strtoupper(trim((string)$rawStudentType));
        $studentType = (str_contains($stUpper, 'MBK') || str_contains($stUpper, 'ABK') || str_contains($stUpper, 'PDBK') || str_contains($stUpper, 'KHUSUS') || str_contains($stUpper, 'INKLUSI')) ? 'PDBK' : 'REGULER';

        $targetClass = $payload['admission_level'] ?? ($payload['target_class'] ?? ($payload['class_program'] ?? 'Kelas 1'));
        if (is_array($targetClass)) {
            $targetClass = $targetClass['name'] ?? 'Kelas 1';
        }

        $wave = $payload['wave'] ?? null;
        if (is_array($wave)) {
            $wave = $wave['name'] ?? null;
        }

        $regType = $payload['registration_type'] ?? ($payload['entry_type'] ?? ($payload['admission_type'] ?? 'Murid Baru'));
        if (is_array($regType)) {
            $regType = $regType['name'] ?? 'Murid Baru';
        }

        $academicYear = $payload['period'] ?? ($payload['academic_year'] ?? null);
        if ($academicYear) {
            $academicYear = str_replace('-', '/', trim((string)$academicYear));
        }

        $regStatus = $payload['registration_status'] ?? ($payload['status'] ?? 'verified');
        if (empty($regStatus)) {
            $regStatus = 'verified';
        }

        $payStatus = $payload['payment_status'] ?? ($payload['spmb_payment_status'] ?? 'unpaid');
        if (empty($payStatus)) {
            $payStatus = 'unpaid';
        }

        $spmbRegId = $payload['id'] ?? ($payload['spmb_registration_id'] ?? null);
        if (!$spmbRegId) {
            $spmbRegId = abs(crc32($regNumber));
        }

        return self::updateOrCreate(
            ['registration_number' => $regNumber],
            [
                'spmb_registration_id' => $spmbRegId,
                'full_name' => $bio['full_name'] ?? ($payload['candidate_name'] ?? ($payload['full_name'] ?? 'Calon Siswa')),
                'nickname' => $bio['nickname'] ?? null,
                'gender' => $gender,
                'birth_place' => $bio['birth_place'] ?? null,
                'birth_date' => $birthDate,
                'nik' => $bio['nik'] ?? null,
                'nisn' => $bio['nisn'] ?? null,
                'no_kk' => $bio['no_kk'] ?? ($parents['no_kk'] ?? null),
                'child_number' => $bio['child_number'] ?? null,
                'siblings_count' => $bio['siblings_count'] ?? null,
                'blood_type' => $bio['blood_type'] ?? null,
                'weight' => $bio['weight'] ?? null,
                'height' => $bio['height'] ?? null,

                'student_type' => $studentType,
                'special_needs_type' => $payload['special_needs_type'] ?? ($bio['special_needs_type'] ?? ($bio['special_needs'] ?? null)),

                // Academic
                'target_unit' => $unit['code'] ?? ($unit['name'] ?? ($payload['target_unit'] ?? 'SD')),
                'target_class' => $targetClass,
                'academic_year' => $academicYear,
                'wave' => $wave,

                // Contact & Parents
                'parent_phone' => $parentPhone,
                'father_name' => $father['name'] ?? null,
                'father_nik' => $father['nik'] ?? null,
                'father_job' => $father['job'] ?? ($father['occupation'] ?? null),
                'father_education' => $father['education'] ?? null,
                'father_phone' => $father['phone'] ?? null,
                'mother_name' => $mother['name'] ?? null,
                'mother_nik' => $mother['nik'] ?? null,
                'mother_job' => $mother['job'] ?? ($mother['occupation'] ?? null),
                'mother_education' => $mother['education'] ?? null,
                'mother_phone' => $mother['phone'] ?? null,
                'guardian_name' => $guardian['name'] ?? null,
                'guardian_phone' => $guardian['phone'] ?? null,

                // Address
                'address' => $fullAddress,
                'rt' => is_array($address) ? ($address['rt'] ?? null) : null,
                'rw' => is_array($address) ? ($address['rw'] ?? null) : null,
                'village' => is_array($address) ? ($address['village'] ?? null) : null,
                'district' => is_array($address) ? ($address['district'] ?? null) : null,
                'city' => is_array($address) ? ($address['city'] ?? null) : null,
                'province' => is_array($address) ? ($address['province'] ?? null) : null,
                'postal_code' => is_array($address) ? ($address['postal_code'] ?? null) : null,

                // School Origin
                'previous_school' => $schoolOrigin['previous_school'] ?? ($payload['previous_school'] ?? null),
                'previous_school_npsn' => $schoolOrigin['npsn'] ?? null,
                'previous_school_address' => $schoolOrigin['school_address'] ?? null,

                // Status
                'spmb_status' => $regStatus,
                'spmb_payment_status' => $payStatus,
                'spmb_verified_at' => $verifiedAt,
                'spmb_registered_at' => $registeredAt,

                // Snapshots
                'documents' => $documents,
                'payments_data' => $payments,
                'raw_payload' => $payload,
            ]
        );
    }
}
