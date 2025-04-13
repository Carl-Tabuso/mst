<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Form4;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use Illuminate\Database\Seeder;

class JobOrderCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::Successful)
            ->create();

        $jobOrder->serviceable->appraisers()->attach(
            Employee::inRandomOrder()->take(rand(2, 4))->get()
        );
        
        $jobOrder->client = fake()->company();
        $jobOrder->address = fake()->address();

        $properties = [];

        foreach ($jobOrder->getDirty() as $key => $value) {
            $properties['before'][$key] = $jobOrder->getOriginal($key);
            $properties['after'][$key] = $value;
        }

        JobOrderCorrection::factory()->create([
            'job_order_id' => $jobOrder->id,
            'properties' => $properties,
            'is_approved' => false,
        ]);
    }
}
