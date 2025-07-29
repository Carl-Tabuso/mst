<?php

namespace Database\Factories;

use App\Enums\MachineStatus;
use App\Models\MachineInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineInfoFactory extends Factory
{
    protected $model = MachineInfo::class;

    public function definition(): array
    {
        return [
            'machine_type'      => $this->faker->randomElement(['Laptop', 'Desktop', 'Printer', 'Scanner', 'Server']),
            'model'             => $this->faker->bothify('Model-####'),
            'serial_no'         => $this->faker->unique()->bothify('SN-########'),
            'tag_no'            => $this->faker->unique()->bothify('TAG-######'),
            'machine_problem'   => $this->faker->optional()->sentence(10),
            'service_performed' => $this->faker->optional()->paragraph(2),
            'recommendation'    => $this->faker->optional()->paragraph(1),
            'machine_status'    => $this->faker->randomElement(MachineStatus::cases())->value,
        ];
    }
}
