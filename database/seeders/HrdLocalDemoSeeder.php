<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkingShift;
use App\Models\BonusSchema;
use App\Models\Holiday;
use App\Models\EmployeeWorkingShift;

class HrdLocalDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Local Shifts Seeding
        $shifts = [
            [
                'code' => 'default',
                'name' => 'Default (Guru & Karyawan Non Shift)',
                'is_shift' => false,
                'description' => 'Jam kerja standar guru dan karyawan non-shift.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '07:30:00', 'end_time' => '12:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'gpk_gpq',
                'name' => 'GPK & GPQ (Guru Pendamping)',
                'is_shift' => false,
                'description' => 'Jam kerja Guru Pendamping Khusus & Guru Pendamping Quran.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '07:00:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'dapur_kebersihan',
                'name' => 'Dapur & Tenaga Kebersihan',
                'is_shift' => false,
                'description' => 'Jam kerja divisi dapur dan tenaga kebersihan.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '06:30:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '06:30:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '06:30:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '06:30:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '06:30:00', 'end_time' => '15:30:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '07:00:00', 'end_time' => '12:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'salehmart_s1',
                'name' => 'Salehmart Shift 1',
                'is_shift' => true,
                'description' => 'Salehmart Shift 1 (Pagi).',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '06:30:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '06:30:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '06:30:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '06:30:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '06:30:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '07:00:00', 'end_time' => '12:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'salehmart_s2',
                'name' => 'Salehmart Shift 2',
                'is_shift' => true,
                'description' => 'Salehmart Shift 2 (Siang).',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '12:00:00', 'end_time' => '20:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '12:00:00', 'end_time' => '20:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '12:00:00', 'end_time' => '20:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '12:00:00', 'end_time' => '20:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '12:00:00', 'end_time' => '20:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '12:00:00', 'end_time' => '18:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'salehmart_s3',
                'name' => 'Salehmart Shift 3',
                'is_shift' => true,
                'description' => 'Salehmart Shift 3 (Middle).',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '07:30:00', 'end_time' => '15:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '07:30:00', 'end_time' => '15:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '07:30:00', 'end_time' => '15:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '07:30:00', 'end_time' => '15:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '07:30:00', 'end_time' => '15:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '07:30:00', 'end_time' => '12:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => null, 'end_time' => null, 'is_off' => true],
                ]
            ],
            [
                'code' => 'satpam_s1',
                'name' => 'Satpam Shift 1 (Pagi)',
                'is_shift' => true,
                'description' => 'Penjagaan Satpam Shift Pagi.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => '06:00:00', 'end_time' => '14:00:00', 'is_off' => false],
                ]
            ],
            [
                'code' => 'satpam_s2',
                'name' => 'Satpam Shift 2 (Siang)',
                'is_shift' => true,
                'description' => 'Penjagaan Satpam Shift Siang.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => '14:00:00', 'end_time' => '22:00:00', 'is_off' => false],
                ]
            ],
            [
                'code' => 'satpam_s3',
                'name' => 'Satpam Shift 3 (Malam)',
                'is_shift' => true,
                'description' => 'Penjagaan Satpam Shift Malam.',
                'details' => [
                    ['day_of_week' => 1, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 2, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 3, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 4, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 5, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 6, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                    ['day_of_week' => 0, 'start_time' => '22:00:00', 'end_time' => '06:00:00', 'is_off' => false],
                ]
            ]
        ];

        foreach ($shifts as $s) {
            $ws = WorkingShift::updateOrCreate(
                ['code' => $s['code']],
                [
                    'name' => $s['name'],
                    'is_shift' => $s['is_shift'],
                    'description' => $s['description'],
                ]
            );

            $ws->details()->delete();
            foreach ($s['details'] as $det) {
                $ws->details()->create($det);
            }
        }

        // 2. Local Bonus Seeding
        $schema = BonusSchema::updateOrCreate(
            ['name' => 'Skema Kehadiran Utama'],
            ['is_active' => true]
        );

        $schema->tiers()->delete();
        $tiers = [
            ['tier_level' => 1, 'nominal' => 10000.00, 'max_late_minutes' => 0],
            ['tier_level' => 2, 'nominal' => 9000.00, 'max_late_minutes' => 5],
            ['tier_level' => 3, 'nominal' => 8000.00, 'max_late_minutes' => 10],
            ['tier_level' => 4, 'nominal' => 7000.00, 'max_late_minutes' => 15],
            ['tier_level' => 5, 'nominal' => 6000.00, 'max_late_minutes' => 20],
            ['tier_level' => 6, 'nominal' => 5000.00, 'max_late_minutes' => 25],
            ['tier_level' => 7, 'nominal' => 4000.00, 'max_late_minutes' => 30],
            ['tier_level' => 8, 'nominal' => 3000.00, 'max_late_minutes' => 60],
        ];

        foreach ($tiers as $t) {
            $schema->tiers()->create($t);
        }

        // 3. Local Holiday Seeding
        Holiday::updateOrCreate(
            ['original_date' => '2026-07-16'],
            ['name' => 'Tahun Baru Islam (Global Holiday)', 'is_global' => true]
        );

        Holiday::updateOrCreate(
            ['original_date' => '2026-08-17'],
            ['name' => 'Hari Kemerdekaan RI (Global Holiday)', 'is_global' => true]
        );

        // 4. Local Employee Shift Schedule mapping
        EmployeeWorkingShift::updateOrCreate(
            [
                'employee_id' => 1,
                'working_shift_id' => WorkingShift::where('code', 'default')->first()->id,
                'start_date' => '2026-07-01'
            ],
            [
                'end_date' => null
            ]
        );
    }
}
