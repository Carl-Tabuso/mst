<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\CancelledJobOrder;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\Position;
use Illuminate\Database\Seeder;

class ClosedJobOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::Closed)
            ->create();

        $form3 = Form3::factory()->create(['form4_id' => $jobOrder->serviceable->id]);

        $haulerPositionId = Position::firstWhere(['name' => 'Hauler'])->id;
        $haulers          = Employee::factory(rand(10, 12))->create(['position_id' => $haulerPositionId]);

        $form3->haulers()->attach($haulers);

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id,
        ]);
    }
}
