<?php

namespace Database\Factories;

use App\Models\ITService;
use App\Models\Employee;
use App\Models\Position;
use App\Models\MachineInfo;
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
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (ITService $itService) {
            $technicianPositionId = Position::where('name', 'Technician')->value('id');
            $technicianIds = Employee::where('position_id', $technicianPositionId)
                ->inRandomOrder()
                ->limit(rand(1, 3))
                ->pluck('id');
            $itService->technicians()->attach($technicianIds);

            MachineInfo::factory(rand(1, 3))->create([
                'it_service_id' => $itService->id,
            ]);
        });
    }
}
