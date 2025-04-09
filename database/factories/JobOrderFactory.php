<?php

namespace Database\Factories;

use App\Enums\JobOrderStatus;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOrder>
 */
class JobOrderFactory extends Factory
{
    use RandomEmployee;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validStatuses = array_filter(
            JobOrderStatus::cases(), 
            fn ($case) => ! in_array(
                $case, 
                JobOrderStatus::getCancelledStatuses()
            )
        );

        return [
            'date_time' => now(),
            'client' => fake()->company(),
            'address' => fake()->address(),
            'department' => fake()->word(),
            'contact_no' => fake()->unique()->numerify('09#########'),
            'contact_person' => fake()->name(),
            'created_by' => $this->getByPosition('Frontliner'),
            'status' => fake()->randomElement($validStatuses),
        ];
    }

    public function status(JobOrderStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status->value
        ]);
    }
}
