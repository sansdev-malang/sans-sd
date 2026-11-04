<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Aligned with Kurikulum Merdeka and SANS Rapor standard nomenclature.
     */
    public function run(): void
    {
        $subjects = [
            [
                'id' => 1,
                'unit' => 'sd',
                'code' => 'PAI',
                'name' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'short_name' => 'PAI',
                'group' => 'A',
                'order' => 1,
                'description' => 'Mata pelajaran PAI dan Budi Pekerti',
            ],
            [
                'id' => 3,
                'unit' => 'sd',
                'code' => 'PP',
                'name' => 'Pendidikan Pancasila',
                'short_name' => 'Pancasila',
                'group' => 'A',
                'order' => 2,
                'description' => 'Mata pelajaran Pendidikan Pancasila / PKn',
            ],
            [
                'id' => 4,
                'unit' => 'sd',
                'code' => 'BIN',
                'name' => 'Bahasa Indonesia',
                'short_name' => 'B. Indonesia',
                'group' => 'A',
                'order' => 3,
                'description' => 'Mata pelajaran Bahasa Indonesia',
            ],
            [
                'id' => 5,
                'unit' => 'sd',
                'code' => 'MTK',
                'name' => 'Matematika',
                'short_name' => 'Matematika',
                'group' => 'A',
                'order' => 4,
                'description' => 'Mata pelajaran Matematika',
            ],
            [
                'id' => 6,
                'unit' => 'sd',
                'code' => 'IPAS',
                'name' => 'Ilmu Pengetahuan Alam dan Sosial (IPAS)',
                'short_name' => 'IPAS',
                'group' => 'A',
                'order' => 5,
                'description' => 'Mata pelajaran IPAS Kurikulum Merdeka',
            ],
            [
                'id' => 7,
                'unit' => 'sd',
                'code' => 'PJOK',
                'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
                'short_name' => 'PJOK',
                'group' => 'A',
                'order' => 6,
                'description' => 'Mata pelajaran Olahraga & Kesehatan',
            ],
            [
                'id' => 8,
                'unit' => 'sd',
                'code' => 'SBK',
                'name' => 'Seni dan Budaya',
                'short_name' => 'Seni Budaya',
                'group' => 'A',
                'order' => 7,
                'description' => 'Mata pelajaran Seni Rupa / Musik / Budaya',
            ],
            [
                'id' => 9,
                'unit' => 'sd',
                'code' => 'BIG',
                'name' => 'Bahasa Inggris',
                'short_name' => 'B. Inggris',
                'group' => 'B',
                'order' => 8,
                'description' => 'Mata pelajaran Bahasa Inggris',
            ],
            [
                'id' => 10,
                'unit' => 'sd',
                'code' => 'BJAWA',
                'name' => 'Bahasa Jawa',
                'short_name' => 'B. Jawa',
                'group' => 'MULOK',
                'order' => 9,
                'description' => 'Muatan Lokal Bahasa Daerah (Bahasa Jawa)',
            ],
            [
                'id' => 2,
                'unit' => 'sd',
                'code' => 'QURAN',
                'name' => 'Pendidikan Al-Qur\'an (Bilqolam & Tahfidz)',
                'short_name' => 'Al-Qur\'an',
                'group' => 'C',
                'order' => 10,
                'description' => 'Program Mengaji, Bilqolam, dan Tahfizh Al-Qur\'an (GPQ)',
            ],
        ];

        foreach ($subjects as $s) {
            Subject::updateOrCreate(
                ['id' => $s['id']],
                [
                    'unit' => $s['unit'] ?? 'sd',
                    'code' => $s['code'],
                    'name' => $s['name'],
                    'short_name' => $s['short_name'],
                    'group' => $s['group'],
                    'order' => $s['order'],
                    'description' => $s['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
