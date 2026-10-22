<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use HasFactory;

    protected $table = 'student_documents';

    protected $fillable = [
        'student_id',
        'document_type',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Formatted file size string.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '-';
        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 1) . ' MB';
        }
        return number_format($this->file_size / 1024, 0) . ' KB';
    }

    /**
     * File URL.
     */
    public function getFileUrlAttribute(): string
    {
        if (!$this->file_path) return '#';
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }
        if (str_starts_with($this->file_path, '/storage/')) {
            return asset(ltrim($this->file_path, '/'));
        }
        return asset('storage/' . $this->file_path);
    }

    /**
     * User-friendly document type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match (strtolower(trim($this->document_type))) {
            'akta_kelahiran', 'akta' => 'Akta Kelahiran',
            'kartu_keluarga', 'kk' => 'Kartu Keluarga (KK)',
            'ktp_ortu', 'ktp' => 'KTP Orang Tua',
            'pas_foto', 'foto' => 'Pas Foto Murid',
            'kia_imunisasi', 'kia', 'imunisasi' => 'Buku KIA / Riwayat Imunisasi',
            'piagam_prestasi', 'piagam' => 'Piagam / Sertifikat Prestasi',
            'ijazah_stsb', 'ijazah' => 'Ijazah / SKL TK/PAUD',
            'surat_mutasi' => 'Surat Pindah / Mutasi',
            default => 'Dokumen Lainnya',
        };
    }

    /**
     * Lucide file icon.
     */
    public function getFileIconAttribute(): string
    {
        $ext = strtolower($this->file_extension ?? pathinfo($this->file_path, PATHINFO_EXTENSION));
        return match ($ext) {
            'pdf' => 'file-text',
            'doc', 'docx' => 'file-text',
            'xls', 'xlsx' => 'file-spreadsheet',
            'jpg', 'jpeg', 'png', 'webp' => 'image',
            default => 'file',
        };
    }
}
