<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\PerformanceCategory;
use App\Models\PerformanceRating;
use App\Models\Position;
use App\Models\TeamLeaderPerformance;
use App\Models\TeamLeaderRating;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamLeaderPerformanceSeeder extends Seeder
{
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

        $form3 = Form3::factory()
            ->create([
                'form4_id' => $jobOrder->serviceable->id,
                'from'     => now()->subDays(5),
                'to'       => now(),
            ]);

        $position = Position::firstWhere(['name' => 'Hauler']);
        $haulers  = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);
        $form3->haulers()->attach($haulers);

        $employeeIds = array_merge($haulers->pluck('id')->toArray(), [
            $form3->team_driver,
            $form3->safety_officer,
            $form3->team_mechanic,
        ]);

        $assignedEmployees = [];
        foreach ($employeeIds as $employeeId) {
            $assignedEmployees[] = [
                'job_order_id' => $jobOrder->id,
                'evaluator_id' => $employeeId,
                'evaluatee_id' => $form3->team_leader,
            ];
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
