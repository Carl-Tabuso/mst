<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\CancelledJobOrder;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3AssignedPersonnel;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;

class ClosedJobOrderSeeder extends Seeder
{
    use RandomEmployee;

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

        $days = now()->subWeek();

        $haulings = Form3Hauling::factory(3)
            ->sequence(fn () => ['date' => $days->addDay()])
            ->create(['form3_id' => $form3->id]);

        $position = Position::firstWhere(['name' => 'Hauler']);
        $haulers  = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);

        $haulings->each(fn ($hauling) => $hauling->haulers()->attach($haulers));

        Form3AssignedPersonnel::factory()->create([
            'form3_hauling_id' => $haulings->first()->id,
            'team_leader'      => $this->getByPosition('Team Leader')->id,
            'team_driver'      => $this->getByPosition('Driver')->id,
            'safety_officer'   => $this->getByPosition('Safety Officer')->id,
            'team_mechanic'    => $this->getByPosition('Mechanic')->id,
        ]);

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id,
        ]);
    }
}
