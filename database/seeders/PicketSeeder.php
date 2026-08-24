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
        // 1. Create Picket Areas with jobs and duty hours similar to the poster
        $areasData = [
            [
                'name' => 'IN FRONT OF MOSQUE/SECURITY AREA',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Greeting the students with Salam, Smile, Greet, Salim\nGreet the parents who drop off the students\nMake sure the students surveillance and safety in front of mosque / security area\nDirecting vehicles and parking order",
                'is_active' => true,
            ],
            [
                'name' => 'IN FRONT OF INNER GATE',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Greeting the students with Salam, Smile, Greet, Salim\nMake sure the students surveillance and safety in front of inner gate",
                'is_active' => true,
            ],
            [
                'name' => 'AROUND CANTEEN',
                'duty_hours' => '06.30 - 07.00',
                'jobs' => "Greeting the students with Salam, Smile, Greet, Salim\nMake sure the students surveillance and safety in canteen area\nMake sure students enter the classes at 06.55",
                'is_active' => true,
            ]
        ];

        $createdAreas = [];
        foreach ($areasData as $areaVal) {
            $createdAreas[] = PicketArea::firstOrCreate(
                ['name' => $areaVal['name']],
                $areaVal
            );
        }

        // 2. Fetch Active SD Employees
        $employees = Employee::where('unit', 'sd')->where('status', 'Active')->get();
        if ($employees->isEmpty()) {
            $employees = Employee::take(15)->get();
        }

        if ($employees->isEmpty()) {
            return;
        }

        // 3. Assign teachers round-robin for days 1 to 6 (Monday to Saturday)
        $empIndex = 0;
        $empCount = $employees->count();

        // Let's clear existing schedules first to avoid duplication
        PicketSchedule::truncate();

        for ($day = 1; $day <= 6; $day++) {
            foreach ($createdAreas as $area) {
                // Assign a teacher
                $employee = $employees[$empIndex % $empCount];
                PicketSchedule::create([
                    'picket_area_id' => $area->id,
                    'day_of_week' => $day,
                    'employee_id' => $employee->id,
                ]);
                $empIndex++;
            }
        }
    }
}
