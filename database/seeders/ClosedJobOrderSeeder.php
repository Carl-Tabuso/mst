<?php

namespace Database\Seeders;

use App\Enums\HaulingStatus;
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
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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

        $form3 = Form3::factory()->create([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => $from = Carbon::parse(fake()->dateTimeBetween('-2 months', '-3 weeks')),
            'to'       => $to   = $from->copy()->addDays(3),
        ]);

        $haulings = Form3Hauling::factory()
            ->count((int) $from->diffInDays($to) + 1)
            ->sequence(fn (Sequence $sequence) => ['date' => $from->copy()->addDays($sequence->index)])
            ->status(HaulingStatus::Done)
            ->create(['form3_id' => $form3->id]);

        $position = Position::firstWhere(['name' => 'Hauler']);
        $haulers  = Employee::query()
            ->where('position_id', $position->id)
            ->inRandomOrder()
            ->take(mt_rand(10, 12))
            ->get();

        $haulings->each(function ($hauling) use ($haulers) {
            $hauling->checklist()->create()->checkAllFields();

            $hauling->haulers()->attach($haulers);
        });

        Form3AssignedPersonnel::factory()->create([
            'form3_hauling_id' => $haulings->first()->id,
            'team_leader'      => $this->getByPosition('Team Leader')->id,
            'safety_officer'   => $this->getByPosition('Safety Officer')->id,
            'team_mechanic'    => $this->getByPosition('Mechanic')->id,
        ]);

        CancelledJobOrder::factory()->create([
            'job_order_id' => $jobOrder->id,
        ]);
    }
}
