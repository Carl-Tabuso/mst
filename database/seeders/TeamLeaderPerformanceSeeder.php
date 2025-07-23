<?php

namespace Database\Seeders;

use App\Enums\HaulingStatus;
use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\PerformanceCategory;
use App\Models\PerformanceRating;
use App\Models\Position;
use App\Models\TeamLeaderPerformance;
use App\Models\TeamLeaderRating;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TeamLeaderPerformanceSeeder extends Seeder
{
    use RandomEmployee;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobOrder = JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->status(JobOrderStatus::Completed)
            ->create();

        $jobOrder->serviceable->appraisers()->attach(
            Employee::inRandomOrder()->take(rand(2, 4))->get()
        );

        $form3 = Form3::factory()->create([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => $from = Carbon::parse(fake()->dateTimeBetween('-2 months', '-3 weeks')),
            'to'       => $to   = $from->copy()->addDays(4),
        ]);

        $haulings = Form3Hauling::factory()
            ->count((int) $from->diffInDays($to) + 1)
            ->sequence(fn (Sequence $sequence) => ['date' => $from->copy()->addDays($sequence->index)])
            ->status(HaulingStatus::Done)
            ->create(['form3_id' => $form3->id]);

        $assignedEmployees = [];

        foreach ($haulings as $hauling) {
            $personnel = $hauling->assignedPersonnel()->create([
                'team_leader'    => $this->getByPosition('Team Leader')->id,
                'team_driver'    => $this->getByPosition('Driver')->id,
                'safety_officer' => $this->getByPosition('Safety Officer')->id,
                'team_mechanic'  => $this->getByPosition('Mechanic')->id,
            ]);

            $position = Position::firstWhere(['name' => 'Hauler']);
            $haulers  = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);
            $hauling->haulers()->attach($haulers);
            $hauling->checklist()->create()->checkAllFields();

            $employeeIds = array_merge($haulers->pluck('id')->toArray(), [
                $personnel->team_driver,
                $personnel->safety_officer,
                $personnel->team_mechanic,
            ]);

            foreach ($employeeIds as $employeeId) {
                $assignedEmployees[] = [
                    'job_order_id' => $jobOrder->id,
                    'evaluator_id' => $employeeId,
                    'evaluatee_id' => $personnel->team_leader,
                ];
            }
        }

        DB::transaction(fn () => TeamLeaderPerformance::insert($assignedEmployees));

        $teamLeaderRatings = [];

        foreach (TeamLeaderPerformance::all() as $performance) {
            foreach (PerformanceCategory::all() as $category) {
                $teamLeaderRatings[] = [
                    'team_leader_performance_id' => $performance->id,
                    'performance_category_id'    => $category->id,
                    'performance_rating_id'      => PerformanceRating::inRandomOrder()->first()->id,
                ];
            }
        }

        DB::transaction(fn () => TeamLeaderRating::insert($teamLeaderRatings));
    }
}
