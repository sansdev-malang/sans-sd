<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuestBookCategory extends Model
{
    use HasFactory;

    protected $table = 'guest_book_categories';

    protected $fillable = [
        'name',
        'slug',
        'sub_label',
        'icon',
        'color',
        'form_type',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Color to Tailwind Badge Style mapping.
     */
    public function getBadgeClassAttribute(): string
    {
        return match (strtolower((string)$this->color)) {
            'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'amber' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'violet' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-950/40 dark:text-violet-300 dark:border-violet-800',
            'cyan' => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
            'rose' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'sky' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
            'teal' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    /**
     * Text color class.
     */
    public function getTextColorAttribute(): string
    {
        return match (strtolower((string)$this->color)) {
            'indigo' => 'text-indigo-600 dark:text-indigo-400',
            'emerald' => 'text-emerald-600 dark:text-emerald-400',
            'amber' => 'text-amber-600 dark:text-amber-400',
            'violet' => 'text-violet-600 dark:text-violet-400',
            'cyan' => 'text-cyan-600 dark:text-cyan-400',
            'rose' => 'text-rose-600 dark:text-rose-400',
            'sky' => 'text-sky-600 dark:text-sky-400',
            'teal' => 'text-teal-600 dark:text-teal-400',
            default => 'text-slate-600 dark:text-slate-400',
        };
    }

    /**
     * Border active color for public kiosk cards.
     */
    public function getBorderActiveClassAttribute(): string
    {
        return match (strtolower((string)$this->color)) {
            'indigo' => 'border-indigo-600 bg-indigo-50/70 shadow-sm ring-2 ring-indigo-500/20',
            'emerald' => 'border-emerald-600 bg-emerald-50/70 shadow-sm ring-2 ring-emerald-500/20',
            'amber' => 'border-amber-600 bg-amber-50/70 shadow-sm ring-2 ring-amber-500/20',
            'violet' => 'border-violet-600 bg-violet-50/70 shadow-sm ring-2 ring-violet-500/20',
            'cyan' => 'border-cyan-600 bg-cyan-50/70 shadow-sm ring-2 ring-cyan-500/20',
            'rose' => 'border-rose-600 bg-rose-50/70 shadow-sm ring-2 ring-rose-500/20',
            'sky' => 'border-sky-600 bg-sky-50/70 shadow-sm ring-2 ring-sky-500/20',
            'teal' => 'border-teal-600 bg-teal-50/70 shadow-sm ring-2 ring-teal-500/20',
            default => 'border-slate-600 bg-slate-50/70 shadow-sm ring-2 ring-slate-500/20',
        };
    }

    /**
     * Active tab border and text color for admin index tabs.
     */
    public function getActiveTabClassAttribute(): string
    {
        return match (strtolower((string)$this->color)) {
            'indigo' => 'border-indigo-500 text-indigo-700 dark:text-indigo-400',
            'emerald' => 'border-emerald-500 text-emerald-700 dark:text-emerald-400',
            'amber' => 'border-amber-500 text-amber-700 dark:text-amber-400',
            'violet' => 'border-violet-500 text-violet-700 dark:text-violet-400',
            'cyan' => 'border-cyan-500 text-cyan-700 dark:text-cyan-400',
            'rose' => 'border-rose-500 text-rose-700 dark:text-rose-400',
            'sky' => 'border-sky-500 text-sky-700 dark:text-sky-400',
            'teal' => 'border-teal-500 text-teal-700 dark:text-teal-400',
            default => 'border-slate-500 text-slate-700 dark:text-slate-300',
        };
    }

    /**
     * Active count badge in tab.
     */
    public function getActiveTabBadgeClassAttribute(): string
    {
        return match (strtolower((string)$this->color)) {
            'indigo' => 'bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300',
            'emerald' => 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300',
            'amber' => 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300',
            'violet' => 'bg-violet-100 dark:bg-violet-950/80 text-violet-700 dark:text-violet-300',
            'cyan' => 'bg-cyan-100 dark:bg-cyan-950/80 text-cyan-700 dark:text-cyan-300',
            'rose' => 'bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300',
            'sky' => 'bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300',
            'teal' => 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300',
            default => 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200',
        };
    }

    /**
     * List of supported Form Type Presets.
     */
    public static function formTypes(): array
    {
        return [
            'umum' => 'Umum (Nama, Kontak, Instansi, Alamat/Kota, Keperluan, Kesan)',
            'dinas' => 'Kedinasan (Pejabat, Kedinasan, Jabatan, Agenda Kedinasan, Kesan)',
            'puskesmas' => 'Pelayanan Kesehatan (Petugas, Faskes, Jabatan, Layanan Kesehatan)',
            'spmb' => 'SPMB (Orang Tua, Calon Murid, Tgl Lahir, Riwayat Terapi, Alamat)',
            'offering' => 'Offering / Marketing (Sales/Perwakilan, Perusahaan/Vendor, Produk Ditawarkan)',
            'penelitian' => 'Penelitian / Observasi (Peneliti, Asal Kampus, Jurusan/Prodi, Judul Riset)',
            'magang' => 'Magang / PKL / PPL (Siswa/Mahasiswa, Asal Sekolah/Kampus, Periode)',
            'studi_banding' => 'Studi Banding / Tiru (Pimpinan Rombongan, Asal Lembaga, Fokus Tiru)',
        ];
    }

    /**
     * List of available Theme Colors.
     */
    public static function colors(): array
    {
        return [
            'slate' => 'Abu-abu (Slate)',
            'indigo' => 'Biru Tua (Indigo)',
            'emerald' => 'Hijau (Emerald)',
            'amber' => 'Kuning / Oranye (Amber)',
            'violet' => 'Ungu (Violet)',
            'cyan' => 'Biru Muda (Cyan)',
            'rose' => 'Merah Muda (Rose)',
            'sky' => 'Biru Langit (Sky)',
            'teal' => 'Hijau Toska (Teal)',
        ];
    }

    /**
     * List of Recommended Icons.
     */
    public static function popularIcons(): array
    {
        return [
            'users' => 'Users (Tamu Umum / Pengunjung)',
            'landmark' => 'Landmark (Dinas / Instansi Pemerintah)',
            'building-2' => 'Building (Sekolah / Kampus / Gedung)',
            'activity' => 'Activity (Kesehatan / Puskesmas / Medis)',
            'user-plus' => 'User Plus (Pendaftaran / SPMB / Murid Baru)',
            'briefcase' => 'Briefcase (Offering / Marketing / Bisnis)',
            'graduation-cap' => 'Graduation Cap (Penelitian / Akademik)',
            'user-check' => 'User Check (Magang / PKL / PPL)',
            'clipboard-list' => 'Clipboard List (Pemeriksaan / Monitoring)',
            'message-square' => 'Message Square (Konsultasi / Audiensi)',
            'heart-handshake' => 'Heart Handshake (Kemitraan / Kerja Sama)',
            'book-open' => 'Book Open (Edukasi / Literasi)',
            'file-text' => 'File Text (Administrasi)',
            'presentation' => 'Presentation (Sosialisasi / Presentasi)',
        ];
    }
}
