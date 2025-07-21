<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\JobOrderCorrection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JobOrderCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::PreHauling)
            ->create();

        $jobOrder->serviceable->appraisers()->attach(
            Employee::inRandomOrder()->take(rand(2, 4))->get()
        );

        Form3::factory()->create([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => null,
            'to'       => null,
        ]);

        $jobOrder->client  = fake()->company();
        $jobOrder->address = fake()->address();

        $properties = [];

        foreach ($jobOrder->getDirty() as $key => $value) {
            $properties['before'][$key] = $jobOrder->getOriginal($key);
            $properties['after'][$key]  = $value;
        }

        JobOrderCorrection::factory()->create([
            'job_order_id' => $jobOrder->id,
            'properties'   => $properties,
        ]);
    }
}
