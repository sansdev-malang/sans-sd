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
        'student_photo_url',
        'formatted_documents'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getRegistrationStatusAttribute(): string
    {
        return $this->attributes['spmb_status'] ?? ($this->attributes['registration_status'] ?? 'verified');
    }

    public function getPaymentStatusAttribute(): string
    {
        return $this->attributes['spmb_payment_status'] ?? ($this->attributes['payment_status'] ?? 'unpaid');
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
            'special_needs_assessment_path' => 'Asesmen Kebutuhan Khusus (Jika Ada)',
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
     * Helper untuk format nomor telepon WhatsApp
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $phone = $this->parent_phone ?: ($this->father_phone ?: $this->mother_phone);
        if (!$phone) return null;

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $appName = function_exists('setting') ? setting('app_name', 'SD Anak Saleh') : 'SD Anak Saleh';
        $message = urlencode("Assalamu'alaikum wr. wb. Ayah/Bunda dari ananda *{$this->full_name}*, kami dari *{$appName}* ingin menginformasikan terkait data pendaftaran SPMB.");
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
        return strtoupper(substr($this->full_name ?? 'S', 0, 2));
    }
}
