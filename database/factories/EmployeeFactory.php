<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'nik' => $this->faker->numerify('################'),
            'nuptk' => $this->faker->numerify('################'),
            'niy' => $this->faker->numerify('########'),
            'employee_type_id' => \App\Models\EmployeeType::firstOrCreate(
                ['code' => 'teacher'],
                ['name' => 'Guru', 'description' => 'Tenaga Pendidik']
            )->id,
            'unit' => $this->faker->randomElement(['paud', 'sd', 'smp']),
            'position' => $this->faker->jobTitle(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'employment_status' => 'PNS',
            'zkteco_uid' => $this->faker->unique()->numberBetween(1000, 9999),
            'status' => 'Active',
        ];
    }
}
