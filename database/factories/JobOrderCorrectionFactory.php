<?php

namespace Database\Factories;

use App\Models\JobOrder;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOrderCorrection>
 */
class JobOrderCorrectionFactory extends Factory
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
            'job_order_id' => JobOrder::inRandomOrder()->first(),
            'properties' => null,
            'is_approved' => fake()->boolean(),
        ];
    }
}
