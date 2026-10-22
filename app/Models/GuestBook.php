<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestBook extends Model
{
    use HasFactory;

    protected $table = 'guest_books';

    protected $fillable = [
        'name',
        'phone',
        'category',
        'student_name',
        'student_birth_date',
        'therapy_history',
        'therapy_diagnosis',
        'institution',
        'position',
        'occupation',
        'address',
        'city',
        'province',
        'purpose',
        'host_recipient',
        'feedback',
        'date',
        'time',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
        'student_birth_date' => 'date',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Cleaned phone number for WhatsApp link.
     */
    public function getCleanPhoneAttribute(): ?string
    {
        if (!$this->phone) return null;
        $clean = preg_replace('/[^0-9]/', '', (string)$this->phone);
        if (str_starts_with($clean, '08')) {
            $clean = '628' . substr($clean, 2);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '628' . substr($clean, 1);
        }
        return $clean;
    }

    /**
     * Direct WhatsApp URL with friendly greeting.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $phone = $this->clean_phone;
        if (!$phone || strlen($phone) < 8) return null;

        $schoolName = function_exists('setting') ? setting('unit_name', 'SD Anak Saleh Malang') : 'SD Anak Saleh Malang';
        $greeting = urlencode("Halo Bapak/Ibu {$this->name},\nTerima kasih telah berkunjung ke {$schoolName}. Ada yang dapat kami bantu lebih lanjut mengenai kunjungan Anda?");
        return "https://wa.me/{$phone}?text={$greeting}";
    }

    /**
     * Formatted time string.
     */
    public function getFormattedTimeAttribute(): string
    {
        if (!$this->time) return '-';
        return substr((string)$this->time, 0, 5) . ' WIB';
    }

    /**
     * Category badge color class.
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return match (strtolower(trim($this->category))) {
            'dinas' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'puskesmas/dinkes', 'puskesmas', 'dinkes' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'spmb' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }
}
