<?php

namespace Database\Seeders;

use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;
use App\Models\CancelledJobOrder;

class DroppedJobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->dropped()
            ->create();

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id
        ]);
    }
}
