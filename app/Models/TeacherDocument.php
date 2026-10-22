<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherDocument extends Model
{
    use HasFactory;

    protected $table = 'teacher_documents';

    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'semester',
        'category',
        'title',
        'files',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'notes',
        'status',
        'feedback',
        'uploaded_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'files' => 'array',
        'reviewed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    protected $appends = [
        'formatted_file_size',
        'file_url',
        'file_icon',
        'category_badge_class',
        'status_badge_class',
        'files_list',
        'total_files_count',
    ];

    /**
     * The teacher (Employee) who owns the document.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    /**
     * Academic Year of the document.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * The user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * The supervisor / principal who reviewed the document.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Formatted file size string (KB / MB).
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
     * Public storage URL for the file.
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
     * Category badge color styling.
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return match (strtoupper(trim($this->category))) {
            'RPP', 'MODUL AJAR' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
            'PROTA' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
            'PROSEM' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
            'RKAS' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'LPJ' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    /**
     * Status badge color styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match (strtolower(trim($this->status))) {
            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'perlu revisi' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
        };
    }

    /**
     * Lucide icon name according to file extension.
     */
    public function getFileIconAttribute(): string
    {
        $ext = strtolower($this->file_extension ?? pathinfo($this->file_path, PATHINFO_EXTENSION));
        return match ($ext) {
            'pdf' => 'file-text',
            'doc', 'docx' => 'file-text',
            'xls', 'xlsx', 'csv' => 'file-spreadsheet',
            'zip', 'rar', '7z' => 'archive',
            'jpg', 'jpeg', 'png', 'webp' => 'image',
            default => 'file',
        };
    }

    /**
     * Get array of all attached files with formatted metadata.
     */
    public function getFilesListAttribute(): array
    {
        if (!empty($this->files) && is_array($this->files)) {
            return array_values(array_map(function ($f) {
                $filePath = $f['file_path'] ?? '';
                $fileName = $f['file_name'] ?? basename($filePath);
                $ext = strtolower($f['file_extension'] ?? pathinfo($fileName, PATHINFO_EXTENSION));
                $size = (int) ($f['file_size'] ?? 0);

                $formattedSize = '-';
                if ($size >= 1048576) {
                    $formattedSize = number_format($size / 1048576, 1) . ' MB';
                } elseif ($size > 0) {
                    $formattedSize = number_format($size / 1024, 0) . ' KB';
                }

                $icon = match ($ext) {
                    'pdf' => 'file-text',
                    'doc', 'docx' => 'file-text',
                    'xls', 'xlsx', 'csv' => 'file-spreadsheet',
                    'zip', 'rar', '7z' => 'archive',
                    'jpg', 'jpeg', 'png', 'webp' => 'image',
                    default => 'file',
                };

                return [
                    'title' => $f['title'] ?? $fileName,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $size,
                    'file_extension' => $ext,
                    'formatted_size' => $formattedSize,
                    'icon' => $icon,
                    'url' => !empty($filePath) ? (str_starts_with($filePath, '/storage/') ? asset(ltrim($filePath, '/')) : asset('storage/' . $filePath)) : '#',
                ];
            }, $this->files));
        }

        if (!empty($this->file_path)) {
            return [[
                'title' => $this->title,
                'file_name' => $this->file_name ?? basename($this->file_path),
                'file_path' => $this->file_path,
                'file_size' => (int) $this->file_size,
                'file_extension' => strtolower($this->file_extension ?: pathinfo($this->file_path, PATHINFO_EXTENSION)),
                'formatted_size' => $this->formatted_file_size,
                'icon' => $this->file_icon,
                'url' => $this->file_url,
            ]];
        }

        return [];
    }

    /**
     * Total count of attached files.
     */
    public function getTotalFilesCountAttribute(): int
    {
        return count($this->files_list);
    }
}
