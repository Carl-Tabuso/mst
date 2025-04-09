<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;
use App\Models\CancelledJobOrder;

class FailedProposalJobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::Failed)
            ->create();

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id
        ]);
    }
}
