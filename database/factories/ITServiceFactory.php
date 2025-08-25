<?php
namespace Database\Factories;

use App\Enums\ITServiceStatus;
use App\Models\Employee;
use App\Models\ITService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ITServiceFactory extends Factory
{
    protected $model = ITService::class;

    public function definition(): array
    {
        $technician = Employee::whereHas('position', fn($q) => $q->where('name', 'Technician'))
            ->inRandomOrder()
            ->first();

        return [
            'technician_id'   => $technician?->id,
            'machine_type'    => $this->faker->randomElement(['Printer', 'Laptop', 'Desktop']),
            'model'           => $this->faker->bothify('Model-###'),
            'serial_no'       => strtoupper(Str::random(10)),
            'tag_no'          => strtoupper(Str::random(6)),
            'machine_problem' => $this->faker->sentence(8),
            'status'          => ITServiceStatus::ForCheckUp,
        ];
    }
}
