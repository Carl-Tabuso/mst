<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\CancelledJobOrder;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class DroppedJobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::Dropped)
            ->create();

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id,
        ]);
    }
}
