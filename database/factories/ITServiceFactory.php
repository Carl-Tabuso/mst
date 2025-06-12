<?php

namespace Database\Factories;

use App\Enums\MachineStatus;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ITService>
 */
class ITServiceFactory extends Factory
{
    use RandomEmployee;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'machine_type'      => fake()->randomElement(['Laptop', 'Desktop', 'Printer', 'Scanner', 'Server']),
            'model'             => fake()->bothify('Model-####'),
            'serial_no'         => fake()->unique()->bothify('SN-########'),
            'tag_no'            => fake()->unique()->bothify('TAG-######'),
            'marchine_problem'  => fake()->optional()->sentence(10),
            'service_performed' => fake()->optional()->paragraph(2),
            'recommendation'    => fake()->optional()->paragraph(1),
            'machine_status'    => fake()->randomElement(MachineStatus::cases()),
            'cse'               => $this->getByPosition('Technician'),
        ];
    }
}
