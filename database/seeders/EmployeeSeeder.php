<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherType = \App\Models\EmployeeType::firstOrCreate(
            ['code' => 'teacher'],
            ['name' => 'Guru', 'description' => 'Tenaga Pendidik']
        );

        $employeeType = \App\Models\EmployeeType::firstOrCreate(
            ['code' => 'employee'],
            ['name' => 'Staf / Karyawan', 'description' => 'Tenaga Kependidikan']
        );

        $managementType = \App\Models\EmployeeType::firstOrCreate(
            ['code' => 'management'],
            ['name' => 'Manajemen', 'description' => 'Unsur Pimpinan / Manajemen']
        );

        // Add Teachers
        $teachers = [
            [
                'name' => 'Drs. Eko Wibowo, M.Pd',
                'email' => 'eko.wibowo@sans.dev',
                'nuptk_nip_nik' => '197508122003121002',
                'type' => 'teacher',
                'unit' => 'sd',
                'subject_position' => 'Matematika',
                'gender' => 'Male',
                'employment_status' => 'PNS',
                'zkteco_uid' => 101,
                'status' => 'Active',
            ],
            [
                'name' => 'Retno Lestari, S.Pd',
                'email' => 'retno.lestari@sans.dev',
                'nuptk_nip_nik' => '198204152009042003',
                'type' => 'teacher',
                'unit' => 'smp',
                'subject_position' => 'Bahasa Inggris',
                'gender' => 'Female',
                'employment_status' => 'PNS',
                'zkteco_uid' => 102,
                'status' => 'Active',
            ],
            [
                'name' => 'Ahmad Fauzi, S.Si',
                'email' => 'ahmad.fauzi@sans.dev',
                'nuptk_nip_nik' => '198810232015041001',
                'type' => 'teacher',
                'unit' => 'sd',
                'subject_position' => 'Fisika',
                'gender' => 'Male',
                'employment_status' => 'Honorer',
                'zkteco_uid' => 103,
                'status' => 'Active',
            ],
            [
                'name' => 'Siti Aminah, S.Pd',
                'email' => 'siti.aminah@sans.dev',
                'nuptk_nip_nik' => '199011032022212009',
                'type' => 'teacher',
                'unit' => 'paud',
                'subject_position' => 'Guru PAUD',
                'gender' => 'Female',
                'employment_status' => 'Tetap Yayasan',
                'zkteco_uid' => 104,
                'status' => 'Active',
            ],
        ];

        $schoolUnit = config('app.school_unit');
        foreach ($teachers as $data) {
            if ($schoolUnit && $data['unit'] !== $schoolUnit) {
                continue;
            }
            $data['employee_type_id'] = $teacherType->id;
            unset($data['type']);
            Employee::create($data);
        }

        // Add Employees (Karyawan)
        $employees = [
            [
                'name' => 'NURAGA ALAM',
                'email' => 'nuraga.alam@sans.dev',
                'nuptk_nip_nik' => '9901238472938472',
                'type' => 'employee',
                'unit' => 'smp',
                'subject_position' => 'Administrasi',
                'gender' => 'Male',
                'employment_status' => 'Tetap Yayasan',
                'zkteco_uid' => 13,
                'status' => 'Active',
            ],
            [
                'name' => 'MEGA WATI',
                'email' => 'mega.wati@sans.dev',
                'nuptk_nip_nik' => '9901238472938473',
                'type' => 'employee',
                'unit' => 'sd',
                'subject_position' => 'Pustakawan',
                'gender' => 'Female',
                'employment_status' => 'Honorer',
                'zkteco_uid' => 14,
                'status' => 'Active',
            ],
            [
                'name' => 'BAMBANG HERIANTO',
                'email' => 'bambang.herianto@sans.dev',
                'nuptk_nip_nik' => '9901238472938474',
                'type' => 'employee',
                'unit' => 'paud',
                'subject_position' => 'Keamanan / Security',
                'gender' => 'Male',
                'employment_status' => 'Tetap Yayasan',
                'zkteco_uid' => 15,
                'status' => 'Active',
            ],
        ];

        foreach ($employees as $data) {
            if ($schoolUnit && $data['unit'] !== $schoolUnit) {
                continue;
            }
            $data['employee_type_id'] = $employeeType->id;
            unset($data['type']);
            Employee::create($data);
        }

        // Seed some attendance logs for today & yesterday
        $allEmployees = Employee::all();
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        foreach ($allEmployees as $employee) {
            // Yesterday's attendance (all present)
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $yesterday,
                'clock_in' => '07:05:00',
                'clock_out' => '15:10:00',
                'status' => 'Present',
                'notes' => 'Tepat waktu',
            ]);

            // Today's attendance
            if ($employee->zkteco_uid == 103) {
                // Sick
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'clock_in' => null,
                    'clock_out' => null,
                    'status' => 'Sick',
                    'notes' => 'Demam tinggi',
                ]);
            } elseif ($employee->zkteco_uid == 15) {
                // Permit
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'clock_in' => null,
                    'clock_out' => null,
                    'status' => 'Permit',
                    'notes' => 'Acara keluarga',
                ]);
            } else {
                // Present
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'clock_in' => '06:55:00',
                    'clock_out' => null,
                    'status' => 'Present',
                    'notes' => 'Hadir pagi',
                ]);
            }
        }

        // Seed user account for Eko Wibowo (Guru)
        $eko = Employee::where('email', 'eko.wibowo@sans.dev')->first();
        if ($eko) {
            \App\Models\User::firstOrCreate(
                ['email' => 'eko@sans.dev'],
                [
                    'name' => $eko->name,
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'employee_id' => $eko->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

