<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\ITService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ITServiceFactory extends Factory
{
    protected $model = ITService::class;

    public function definition(): array
    {
        return [
            'technician_id'   => Employee::inRandomOrder()->first()->id ?? Employee::factory(),
            'machine_type'    => $this->faker->randomElement(['Printer', 'Laptop', 'Desktop']),
            'model'           => $this->faker->bothify('Model-###'),
            'serial_no'       => strtoupper(Str::random(10)),
            'tag_no'          => strtoupper(Str::random(6)),
            'machine_problem' => $this->faker->sentence(8),
        ];
    }
}
