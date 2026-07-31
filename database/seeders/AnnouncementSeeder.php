<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari user pertama sebagai admin pembuat pengumuman
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $announcements = [
            [
                'title' => 'Rapat Evaluasi Bulanan Kinerja Pegawai',
                'content' => '<p>Diberitahukan kepada seluruh pegawai dan guru, bahwa rapat evaluasi bulanan akan dilaksanakan pada:</p><ul><li><strong>Hari/Tanggal:</strong> Jumat, 10 Agustus 2026</li><li><strong>Waktu:</strong> 13.00 WIB - Selesai</li><li><strong>Tempat:</strong> Ruang Rapat Utama</li></ul><p>Dimohon kehadirannya tepat waktu dan membawa laporan kinerja masing-masing. Terima kasih.</p>',
                'category' => 'penting',
                'target_audience' => 'employee',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(1),
                'expiry_date' => Carbon::now()->addDays(7),
                'created_by' => $adminId,
            ],
            [
                'title' => 'Pelaksanaan Lomba Kemerdekaan 17 Agustus',
                'content' => '<p>Halo semuanya! Dalam rangka menyambut HUT Kemerdekaan RI, sekolah kita akan mengadakan berbagai perlombaan menarik. Mari ikut berpartisipasi memeriahkan acara ini!</p><p>Acara akan diselenggarakan di lapangan utama pada tanggal 15-16 Agustus 2026.</p>',
                'category' => 'kegiatan',
                'target_audience' => 'global',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(2),
                'expiry_date' => Carbon::parse('2026-08-17 00:00:00'),
                'created_by' => $adminId,
            ],
            [
                'title' => 'Pembaruan Sistem Akademik (Maintenance)',
                'content' => '<p>Sistem Informasi Akademik dan Dashboard SIAKAD akan mengalami maintenance pada akhir pekan ini selama kurang lebih 4 jam.</p><p><em>Mohon untuk menyimpan seluruh pekerjaan Anda sebelum waktu maintenance.</em></p>',
                'category' => 'umum',
                'target_audience' => 'employee',
                'is_active' => true,
                'publish_date' => Carbon::now(),
                'expiry_date' => Carbon::now()->addDays(3),
                'created_by' => $adminId,
            ],
            [
                'title' => 'Jadwal Ujian Tengah Semester Ganjil 2026',
                'content' => '<p>Ujian Tengah Semester (UTS) Ganjil Tahun Ajaran 2026/2027 akan diselenggarakan mulai tanggal <strong>15 September 2026</strong>. Siswa diharapkan mempersiapkan diri dengan baik.</p>',
                'category' => 'akademik',
                'target_audience' => 'student',
                'is_active' => true,
                'publish_date' => Carbon::now()->subHours(5),
                'expiry_date' => null, // Berlaku seterusnya sampai di-nonaktifkan
                'created_by' => $adminId,
            ],
            [
                'title' => 'Pertemuan Wali Murid Kelas 1 Baru',
                'content' => '<p>Yth. Bapak/Ibu Wali Murid,</p><p>Kami mengundang Bapak/Ibu untuk hadir dalam acara sosialisasi kurikulum Merdeka Belajar untuk siswa kelas 1 yang akan diadakan minggu depan.</p><p>Detail undangan menyusul lewat surat fisik yang dibagikan kepada siswa.</p>',
                'category' => 'penting',
                'target_audience' => 'parent',
                'is_active' => true,
                'publish_date' => Carbon::now()->subDays(3),
                'expiry_date' => Carbon::now()->addDays(14),
                'created_by' => $adminId,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
