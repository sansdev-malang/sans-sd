<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

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

        \App\Models\Setting::set('app_name', 'SANS SD');
        \App\Models\Setting::set('unit_name', 'SANS SD Malang');
        \App\Models\Setting::set('app_copyright', '© 2026 SANS.dev SD School Information System. All rights reserved.');

        $this->call([
            EmployeeSeeder::class,
        ]);

        // \App\Models\ZktecoDevice::firstOrCreate(
        //     ['ip_address' => '192.168.1.201'],
        //     [
        //         'name' => 'Mesin Sidik Jari Gerbang Utama',
        //         'port' => 4370,
        //         'model_name' => 'ZKTeco K40',
        //         'location' => 'Pos Satpam Depan',
        //         'is_online' => true,
        //     ]
        // );

        // \App\Models\ZktecoDevice::firstOrCreate(
        //     ['ip_address' => '192.168.1.202'],
        //     [
        //         'name' => 'Mesin Wajah & Finger Kantor Guru',
        //         'port' => 4370,
        //         'model_name' => 'ZKTeco iFace',
        //         'location' => 'Lobby Kantor Guru',
        //         'is_online' => true,
        //     ]
        // );
    }
}

