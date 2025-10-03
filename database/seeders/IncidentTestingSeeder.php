<?php

namespace Database\Seeders;

use App\Enums\HaulingStatus;
use App\Enums\IncidentStatus;
use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\Incident;
use App\Models\JobOrder;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IncidentTestingSeeder extends Seeder
{
    use RandomEmployee;

    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::ForVerification)
            ->create();

        $jobOrder->serviceable->appraisers()->attach(
            Employee::inRandomOrder()->take(rand(2, 4))->get()
        );

        $form3 = Form3::factory()->create([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => $from = Carbon::parse(fake()->dateTimeBetween('-2 months', endDate: '-3 weeks')),
            'to'       => $to   = $from->copy()->addDays(4),
        ]);

        $teamLeaderID    = Position::firstWhere(['name' => 'Team Leader'])->employees->random()->id;
        $safetyOfficerID = Position::firstWhere(['name' => 'Safety Officer'])->employees->random()->id;

        $haulings = Form3Hauling::factory()
            ->count((int) $from->diffInDays($to) + 1)
            ->sequence(fn (Sequence $sequence) => ['date' => $from->copy()->addDays($sequence->index)])
            ->status(HaulingStatus::Done)
            ->create(['form3_id' => $form3->id]);

        $incidentInserts = $haulings->map(function ($hauling) {
            return [
                'form3_hauling_id' => $hauling->id,
                'created_by'       => null,
                'status'           => IncidentStatus::Draft->value,
                'subject'          => 'Incident Report for Hauling '.$hauling->date->format('Y-m-d'),
                'location'         => 'To be determined',
                'infraction_type'  => 'To be determined',
                'occured_at'       => now(),
                'description'      => 'Auto-generated incident report for hauling operation',
                'is_read'          => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        })->toArray();

        Incident::insert($incidentInserts);

        foreach ($haulings as $hauling) {
            $hauling->assignedPersonnel()->create([
                'team_leader'    => $teamLeaderID,
                'team_driver'    => $this->getByPosition('Driver')->id,
                'safety_officer' => $safetyOfficerID,
                'team_mechanic'  => $this->getByPosition('Mechanic')->id,
            ]);

            $position = Position::firstWhere(['name' => 'Hauler']);
            $haulers  = Employee::query()
                ->where('position_id', $position->id)
                ->inRandomOrder()
                ->take(mt_rand(10, 12))
                ->get();
            $hauling->haulers()->attach($haulers);
            $hauling->checklist()->create()->checkAllFields();
        }
    }
}
