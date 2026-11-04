<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\HomeroomAssignment;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class HomeroomAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ay = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
        if (!$ay) {
            return;
        }

        $gpqSubject = Subject::where('code', 'GPQ')->first();
        $gpqSubjectId = $gpqSubject?->id;

        // Ensure 3B, 5C, 6D have homeroom teachers
        $fixClassrooms = [
            '3B' => 2,   // Hj. Noor Jeehan
            '5C' => 25,  // Moch. Yusroni
            '6D' => 3,   // Sri Yudiyanti
        ];
        foreach ($fixClassrooms as $code => $empId) {
            Classroom::where('academic_year_id', $ay->id)
                ->where('code', $code)
                ->whereNull('homeroom_teacher_id')
                ->update(['homeroom_teacher_id' => $empId]);
        }

        // 1. Wali Kelas from Classrooms
        $classrooms = Classroom::where('academic_year_id', $ay->id)->get()->keyBy('code');

        foreach ($classrooms as $cr) {
            if ($cr->homeroom_teacher_id) {
                HomeroomAssignment::updateOrCreate(
                    [
                        'academic_year_id' => $ay->id,
                        'classroom_id' => $cr->id,
                        'role' => 'wali_kelas',
                    ],
                    [
                        'class_level_id' => $cr->class_level_id,
                        'employee_id' => $cr->homeroom_teacher_id,
                        'is_active' => true,
                        'semester' => 'all',
                    ]
                );
            }
        }

        // 2. Coordinators & Assistants
        $classLevels = ClassLevel::all()->keyBy('order');

        $coordinators = [
            1 => [22],     // Romadhoniar
            2 => [12],     // Ika Wijayanti
            3 => [20, 35], // Paramita Puri & Prima Suci
            4 => [18],     // Nur Rizky Marsa
            5 => [28, 16], // Kusnia & Mokh. Danny
            6 => [24],     // Nihayatul Hasanah
        ];

        foreach ($coordinators as $lvlOrder => $empIds) {
            $lvl = $classLevels[$lvlOrder] ?? null;
            if ($lvl) {
                foreach ($empIds as $empId) {
                    HomeroomAssignment::updateOrCreate(
                        [
                            'academic_year_id' => $ay->id,
                            'class_level_id' => $lvl->id,
                            'employee_id' => $empId,
                            'role' => 'koordinator_tingkat',
                        ],
                        [
                            'classroom_id' => null,
                            'is_active' => true,
                            'semester' => 'all',
                        ]
                    );
                }
            }
        }

        $assistants = [
            1 => [48, 31], // Selvi Dwi Wahyuni & Mohamad Nurahman
        ];

        foreach ($assistants as $lvlOrder => $empIds) {
            $lvl = $classLevels[$lvlOrder] ?? null;
            if ($lvl) {
                foreach ($empIds as $empId) {
                    HomeroomAssignment::updateOrCreate(
                        [
                            'academic_year_id' => $ay->id,
                            'class_level_id' => $lvl->id,
                            'employee_id' => $empId,
                            'role' => 'pendamping_tingkat',
                        ],
                        [
                            'classroom_id' => null,
                            'is_active' => true,
                            'semester' => 'all',
                        ]
                    );
                }
            }
        }

        // 3. Guru Kelas
        $guruKelas = [
            '1A' => 40, // Ari Iswahyudi
            '1B' => 42, // Milatun Nafisah
            '1C' => 43, // Varianta Java
            '1D' => 41, // Suwaibatul Aslamiyah
            '4A' => 8,  // Risas Wahyudi
            '4B' => 11, // Masruhan
            '5C' => 75, // Binti Zakkiyatul
            '5D' => 4,  // Achmad Efendi
            '6C' => 23, // Jaronah
        ];

        foreach ($guruKelas as $code => $empId) {
            $cr = $classrooms[$code] ?? null;
            if ($cr) {
                HomeroomAssignment::updateOrCreate(
                    [
                        'academic_year_id' => $ay->id,
                        'classroom_id' => $cr->id,
                        'employee_id' => $empId,
                        'role' => 'guru_kelas',
                    ],
                    [
                        'class_level_id' => $cr->class_level_id,
                        'is_active' => true,
                        'semester' => 'all',
                    ]
                );
            }
        }

        // 4. GPQ
        $gpqList = [
            '1B' => 76, // Azzahro Maulidiah
            '1C' => 82, // Putri Wahyuning Laili
            '2A' => 69, // Aisyah Ani Rosita
            '2D' => 78, // Rr. Nur Apriyanti
            '3A' => 72, // Sukma Abdul Rozy
            '3B' => 79, // Hurul Jinani
            '4A' => 84, // Muhammad Akbar Amin
            '4C' => 83, // Yusuf Mu'awiyah
            '4D' => 80, // Nabila Ucinanda
            '5A' => 70, // Zulaihah
            '5B' => 73, // Muhammad Wildan Makhasin
            '5D' => 77, // Shofiyatul Mardiyah
            '6A' => 71, // Rima Rahmayanti
            '6C' => 74, // Nurul Akhyar
            '6D' => 81, // Muhammad Mukid
        ];

        foreach ($gpqList as $code => $empId) {
            $cr = $classrooms[$code] ?? null;
            if ($cr) {
                HomeroomAssignment::updateOrCreate(
                    [
                        'academic_year_id' => $ay->id,
                        'classroom_id' => $cr->id,
                        'employee_id' => $empId,
                        'role' => 'gpq',
                    ],
                    [
                        'class_level_id' => $cr->class_level_id,
                        'subject_id' => $gpqSubjectId,
                        'is_active' => true,
                        'semester' => 'all',
                    ]
                );
            }
        }

        // 5. GPK
        $gpkList = [
            '1B' => [50, 68], // Arik Wijayanto, Yetzky Yudistira
            '1C' => [46, 67], // Ika Puspitasari, Ferdivan Andra Farera
            '2A' => [47, 54], // Rikha Dwi Rachmawati, Sukmawati Megawijayanti
            '2B' => [61, 59], // Nurhayati, Sherly Annisa
            '2C' => [62],     // Nur Laili Saadah
            '2D' => [60],     // Akhmad Saiful
            '3A' => [45],     // Nurul Asri Fitriyah
            '3B' => [51],     // Desilfa Dwi Nursavitri
            '3C' => [56],     // Afriska Nur Azizah
            '3D' => [63],     // Muhammad Baha'ul Alamsyah
            '4B' => [57],     // Khofifah Indar Khoiroh
            '4C' => [55],     // Nila Fadilah
            '5A' => [53],     // Tursina Ainun Nisa'
            '5D' => [58],     // Venorica Afdela
            '6A' => [44],     // Yahya Firmansah
            '6B' => [52],     // Ahmad Shobirin
            '6C' => [137],    // Syaifud Dina Fitriana
        ];

        foreach ($gpkList as $code => $empIds) {
            $cr = $classrooms[$code] ?? null;
            if ($cr) {
                foreach ($empIds as $empId) {
                    HomeroomAssignment::updateOrCreate(
                        [
                            'academic_year_id' => $ay->id,
                            'classroom_id' => $cr->id,
                            'employee_id' => $empId,
                            'role' => 'gpk',
                        ],
                        [
                            'class_level_id' => $cr->class_level_id,
                            'is_active' => true,
                            'semester' => 'all',
                        ]
                    );
                }
            }
        }
    }
}

