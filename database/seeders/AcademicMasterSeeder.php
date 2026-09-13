<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class AcademicMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tahun Ajaran
        $ay2627 = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            [
                'code' => '2627',
                'semester' => 'ganjil',
                'is_active' => true,
                'start_date' => '2026-07-15',
                'end_date' => '2027-06-25',
                'description' => 'Tahun Pelajaran 2026/2027 (Berjalan)',
            ]
        );

        $ay2728 = AcademicYear::firstOrCreate(
            ['name' => '2027/2028'],
            [
                'code' => '2728',
                'semester' => 'ganjil',
                'is_active' => false,
                'start_date' => '2027-07-14',
                'end_date' => '2028-06-24',
                'description' => 'Tahun Pelajaran 2027/2028 (SPMB)',
            ]
        );

        // 2. Tingkat Kelas SD (1 - 6)
        $levels = [];
        for ($i = 1; $i <= 6; $i++) {
            $levels[$i] = ClassLevel::firstOrCreate(
                ['code' => (string)$i],
                [
                    'name' => "Kelas {$i}",
                    'order' => $i,
                    'description' => "Jenjang Pendidikan SD Kelas {$i}",
                ]
            );
        }

        // 3. Guru Wali Kelas
        $teachers = Employee::take(15)->pluck('id')->toArray();

        // 4. Rombongan Belajar (Classrooms)
        $rombelNames = [
            1 => [
                ['name' => '1-A (Ibnu Sina)', 'code' => '1A'],
                ['name' => '1-B (Al-Farabi)', 'code' => '1B'],
            ],
            2 => [
                ['name' => '2-A (Ibnu Rusyd)', 'code' => '2A'],
                ['name' => '2-B (Al-Khawarizmi)', 'code' => '2B'],
            ],
            3 => [
                ['name' => '3-A (Al-Biruni)', 'code' => '3A'],
                ['name' => '3-B (Ibnu Batutah)', 'code' => '3B'],
            ],
            4 => [
                ['name' => '4-A (Al-Kindi)', 'code' => '4A'],
                ['name' => '4-B (Jabir bin Hayyan)', 'code' => '4B'],
            ],
            5 => [
                ['name' => '5-A (Al-Zahrawi)', 'code' => '5A'],
                ['name' => '5-B (Ibnu Nafis)', 'code' => '5B'],
            ],
            6 => [
                ['name' => '6-A (Al-Jazari)', 'code' => '6A'],
                ['name' => '6-B (Ibnu Khaldun)', 'code' => '6B'],
            ],
        ];

        $teacherIdx = 0;
        foreach ([$ay2627, $ay2728] as $ay) {
            foreach ($rombelNames as $lvlNum => $rombels) {
                foreach ($rombels as $r) {
                    Classroom::firstOrCreate(
                        [
                            'name' => $r['name'],
                            'academic_year_id' => $ay->id,
                        ],
                        [
                            'code' => $r['code'],
                            'class_level_id' => $levels[$lvlNum]->id,
                            'homeroom_teacher_id' => $teachers[$teacherIdx % max(1, count($teachers))] ?? null,
                            'capacity' => 28,
                            'is_active' => true,
                            'description' => "Rombongan Belajar {$r['name']} T.A. {$ay->name}",
                        ]
                    );
                    $teacherIdx++;
                }
            }
        }
    }
}
