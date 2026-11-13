<?php

namespace Database\Seeders;

use App\Models\EmployeeType;
use App\Models\PicketArea;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Initial Admin Users
        User::firstOrCreate(
            ['email' => 'admin@sans.dev'],
            [
                'name' => 'Admin SD',
                'password' => Hash::make('password'),
                'role' => 'admin_sd',
                'employee_id' => null,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'superadmin1@sans.dev'],
            [
                'name' => 'Super Admin 1',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'employee_id' => null,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'superadmin2@sans.dev'],
            [
                'name' => 'Super Admin 2',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'employee_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // 2. Default App Settings
        Setting::set('app_name', 'SAANS SD');
        Setting::set('unit_name', 'SAANS SD Malang');
        Setting::set('app_copyright', '© 2026 SAANS.dev SD School Information System. All rights reserved.');

        // 3. Employee Types Master
        EmployeeType::firstOrCreate(
            ['code' => 'teacher'],
            ['name' => 'Guru', 'description' => 'Tenaga Pendidik']
        );

        EmployeeType::firstOrCreate(
            ['code' => 'employee'],
            ['name' => 'Staf / Karyawan', 'description' => 'Tenaga Kependidikan']
        );

        EmployeeType::firstOrCreate(
            ['code' => 'management'],
            ['name' => 'Manajemen', 'description' => 'Unsur Pimpinan / Manajemen']
        );

        // 4. Picket Areas Master
        $picketAreas = [
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
            ],
        ];

        foreach ($picketAreas as $area) {
            PicketArea::firstOrCreate(['name' => $area['name']], $area);
        }
    }
}
