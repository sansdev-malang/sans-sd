<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\PicketArea;
use App\Models\PicketSchedule;
use Illuminate\Database\Seeder;

class PicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Picket Areas with exact jobs and duty hours from the poster
        $areasData = [
            [
                'name' => 'IN FRONT OF MOSQUE/SECURITY AREA',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Making sure all teachers are in duty based on schedule (Memastikan seluruh kegiatan piket berjalan sesuai tupoksi)\nGreeting the students with Salam, Smile, Greet, Salim, and Make sure the students surveillance and safety (Menyambut ananda dengan Salam, Senyum, Sapa, Salim, Memastikan pengawasan dan keamanan ananda)",
                'is_active' => true,
            ],
            [
                'name' => 'IN FRONT OF INNER GATE',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Greeting the students with Salam, Smile, Greet, Salim, and Make sure the students surveillance and safety (Menyambut ananda dengan Salam, Senyum, Sapa, Salim, Memastikan pengawasan dan keamanan ananda)",
                'is_active' => true,
            ],
            [
                'name' => 'AROUND CANTEEN',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Greeting the teachers with Salam, Smile, Greet, Shake hand, make sure the students surveillance and safety and also ring the bell on 06.55 a.m (Menyambut guru dengan Salam, Senyum, Sapa, Salaman dan Memastikan pengawasan dan keamanan ananda dan membunyikan bel jam 06.55)",
                'is_active' => true,
            ]
        ];

        $createdAreas = [];
        foreach ($areasData as $areaVal) {
            $createdAreas[$areaVal['name']] = PicketArea::updateOrCreate(
                ['name' => $areaVal['name']],
                $areaVal
            );
        }

        // 2. Fetch Active SD Employees
        $employees = Employee::where('unit', 'sd')->where('status', 'Active')->get();
        if ($employees->isEmpty()) {
            $employees = Employee::get();
        }

        // Clear existing schedules first to avoid duplication
        PicketSchedule::truncate();

        // 3. Fuzzy match helper to map poster teacher names to DB employees
        $findEmployee = function($rawName) use ($employees) {
            // Clean up titles, degrees, and extra symbols to get the core name
            $cleanName = preg_replace('/\b(S\.Pd|S\.AB|S\.Psi|M\.Pd|Gr|M\.Kom|S\.Si|A\.Md\.Keb|S\.TP|S\.Sos|S\.Hum|S\.Ag|M\.Pd\.I|S\.Or|SE|A\.P|Hj|Rr|Soeharto|Ningrum|Susanto|Utomo|Romadhona|Furoida|Betarizqy|Beatrizqy)\b/i', '', $rawName);
            $cleanName = preg_replace('/[.,]/', ' ', $cleanName);
            $cleanName = trim(preg_replace('/\s+/', ' ', $cleanName));
            
            // Try to find the employee by comparing names
            $match = $employees->first(function($emp) use ($cleanName) {
                $dbName = strtolower($emp->name);
                $searchName = strtolower($cleanName);
                return (strpos($dbName, $searchName) !== false) || (strpos($searchName, $dbName) !== false);
            });
            
            // Specific overrides for spelling differences
            if (!$match) {
                if (stripos($rawName, 'Noor Jehhan') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Noor Jeehan') !== false);
                }
                if (stripos($rawName, 'Dini Eko W') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Dini Eko Wulandari') !== false);
                }
                if (stripos($rawName, 'Ferdian Andra') !== false || stripos($rawName, 'Ferdivan') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Ferdivan Andra') !== false);
                }
                if (stripos($rawName, 'Azahro') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Azzahro') !== false);
                }
                if (stripos($rawName, 'Rr. Nur Apriyanti') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Apriyanti') !== false);
                }
                if (stripos($rawName, 'Danny Permata') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Danny') !== false);
                }
                if (stripos($rawName, 'Prima Suci') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Prima Suci') !== false);
                }
                if (stripos($rawName, 'Nur Risky') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Nur Rizky') !== false);
                }
                if (stripos($rawName, 'Romadonair') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Romadhoniar') !== false);
                }
                if (stripos($rawName, 'Yusuf Muawiyah') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Yusuf') !== false);
                }
                if (stripos($rawName, 'Yestky') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Yetzky') !== false);
                }
                if (stripos($rawName, 'Nabila Ucinanda') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Nabila Ucinanda') !== false);
                }
                if (stripos($rawName, 'M. Mukid') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Mukid') !== false);
                }
                if (stripos($rawName, 'M. Baha\'ul') !== false) {
                    return $employees->first(fn($e) => stripos($e->name, 'Baha\'ul') !== false);
                }
            }
            return $match;
        };

        // 4. Poster schedule matrix data
        $scheduleData = [
            // Day 1: Monday
            1 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Herlina Tri Pambudiati',
                    'Ika Wijayanti',
                    'Dini Eko W.',
                    'Ferdian Andra Farerra',
                    'Azahro Maulidiah',
                    'Selvi Dwi Wahyuni',
                    'Sherly Annisa Ramadhani'
                ],
                'IN FRONT OF INNER GATE' => [
                    'Rr. Nur Apriyanti Atika Anggraini',
                    'Ika Su\'udia',
                    'Hurul Jinani',
                    'Prima Suci Ibar Wati',
                    'Ucik Sriwahyuni'
                ],
                'AROUND CANTEEN' => [
                    'Tursina Ainun Nisa\' Caniago',
                    'Mokh. Danny Permata',
                    'Dara Eges Nuryana',
                    'Puri Wiranti'
                ]
            ],
            // Day 2: Tuesday
            2 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Moch Ichsan Wibowo',
                    'Nur Risky Marsa',
                    'Romadonair Fitri Aini',
                    'Gita Noviria',
                    'Ika Puspitasari',
                    'Putri Wahyuning Laili',
                    'Suwaibatul Aslamiyah'
                ],
                'IN FRONT OF INNER GATE' => [
                    'Irdayus Melindra',
                    'Aning Masyrufatin',
                    'Nurul Asri Fitriyah',
                    'Arif Nur Rahman',
                    'Masruhan',
                    'Yusuf Muawiyah'
                ],
                'AROUND CANTEEN' => [
                    'Ghoniyur Rohman',
                    'Anida Nafis Qotrunnada',
                    'Desi Ratnasari',
                    'Rima Rahmayanti',
                    'Jaronah'
                ]
            ],
            // Day 3: Wednesday
            3 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Hadi Susanto',
                    'Paramita Puri Anggraini',
                    'Irma Wahyu Putri Yoditya',
                    'Mu Ida Nur Fadhilah',
                    'Ari Iswahyudi',
                    'Varianta Jaya Yuam Miranda'
                ],
                'IN FRONT OF INNER GATE' => [
                    'Rikha Dwi Rachmawati',
                    'Nur Laili Saadah',
                    'Sukma Abdul Rozy',
                    'Afriska Nur Azizah',
                    'Miftakul Jannah',
                    'Khofifah Indar Khoiroh',
                    'Lailatul Munawaroh'
                ],
                'AROUND CANTEEN' => [
                    'Muhammad Wildan Makhasin',
                    'Venorica Afdela',
                    'Yahya Firmansah',
                    'Nurul Akhyar'
                ]
            ],
            // Day 4: Thursday
            4 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Herlina Tri Pambudiati',
                    'Nihayatul Hasanah',
                    'Nadia Fatma Yanti',
                    'Raga Cahya Taufikurahman',
                    'Milatun Nafisah',
                    'Sukmawati Megawijayanti'
                ],
                'IN FRONT OF INNER GATE' => [
                    'Miftakhul Jannah',
                    'Anis Amelia',
                    'Noor Jehhan',
                    'Desty Arini Mutiara',
                    'Risas Wahyudi',
                    'Sri Subakti',
                    'Hj. Sri Yudiyanti'
                ],
                'AROUND CANTEEN' => [
                    'Moch Yusroni',
                    'Achmad Efendi',
                    'Ainur Rifqi',
                    'Nabila Ucinanda'
                ]
            ],
            // Day 5: Friday
            5 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Elvera Rosana E.',
                    'Kusnia',
                    'Aisyah Ani Rosita',
                    'Arik Wijayanto',
                    'Yestky Yudistira',
                    'Mohamad Nurahman'
                ],
                'IN FRONT OF INNER GATE' => [
                    'Nurhayati',
                    'Akhmad Saiful',
                    'Desilfa Dwi Nursavitri',
                    'M. Baha\'ul Alamsyah Al Faini',
                    'Muhammad Akbar Amin',
                    'Nila Fadilah',
                    'Zulaihah'
                ],
                'AROUND CANTEEN' => [
                    'Binti Zakkiyatul Faqiroh',
                    'Shofiyatul Mardiyah',
                    'Ahmad Shobirin',
                    'M. Mukid'
                ]
            ],
            // Day 6: Saturday
            6 => [
                'IN FRONT OF MOSQUE/SECURITY AREA' => [
                    'Andreas Setiyono',
                    'Herlina Tri Pambudiati',
                    'Elvera Rosana E.',
                    'Hadi Susanto'
                ]
            ]
        ];

        // 5. Seed the schedules using mapping helper
        foreach ($scheduleData as $day => $areas) {
            foreach ($areas as $areaName => $teacherNames) {
                $areaId = $createdAreas[$areaName]->id;
                foreach ($teacherNames as $teacherName) {
                    $employee = $findEmployee($teacherName);
                    if ($employee) {
                        PicketSchedule::create([
                            'picket_area_id' => $areaId,
                            'day_of_week' => $day,
                            'employee_id' => $employee->id,
                        ]);
                    }
                }
            }
        }
    }
}
