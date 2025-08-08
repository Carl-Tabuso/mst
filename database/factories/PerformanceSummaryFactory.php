<?php

namespace Database\Factories;

use App\Enums\JobOrderStatus;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\PerformanceSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceSummaryFactory extends Factory
{
    protected $model = PerformanceSummary::class;

    public function definition(): array
    {
        return [
            'job_order_id'    => JobOrder::factory()->status(JobOrderStatus::Completed)->for(Form4::factory(), 'serviceable'),
            'overall_remarks' => $this->faker->optional()->sentence(12),
        ];
    }

    /**
     * Associate with a specific job order.
     */
    public function forJobOrder(JobOrder $jobOrder): static
    {
        return $this->state(fn (array $attributes) => [
            'job_order_id' => $jobOrder->id,
        ]);
    }
}
