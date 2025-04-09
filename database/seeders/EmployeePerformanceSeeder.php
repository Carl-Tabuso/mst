<?php

namespace Database\Seeders;

use App\Models\Form3;
use App\Models\Form4;
use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\Position;
use App\Models\Form3Hauling;
use App\Enums\JobOrderStatus;
use App\Models\EmployeePerformance;
use App\Models\EmployeeRating;
use App\Traits\RandomEmployee;
use Illuminate\Database\Seeder;
use App\Models\PerformanceRating;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceCategory;

class EmployeePerformanceSeeder extends Seeder
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

        $form3 = Form3::factory()
            ->create(['form4_id' => $jobOrder->serviceable->id]);

        $days = now()->subDays(5);

        $haulings = Form3Hauling::factory(5)
            ->sequence(fn () => ['date' => $days->addDay()])
            ->create(['form3_id' => $form3->id]);

        $assignedEmployees = [];

        foreach ($haulings as $hauling) {
            $personnel = $hauling->assignedPersonnel()->create([
                'team_leader' => $this->getByPosition('Team Leader')->id,
                'team_driver' => $this->getByPosition('Driver')->id,
                'safety_officer' => $this->getByPosition('Safety Officer')->id,
                'team_mechanic' => $this->getByPosition('Mechanic')->id,
            ]);

            $position = Position::firstWhere(['name' => 'Hauler']);
            $haulers = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);
            $hauling->haulers()->attach($haulers);

            $employeeIds = array_merge($haulers->pluck('id')->toArray(), [
                $personnel->team_driver,
                $personnel->safety_officer,
                $personnel->team_mechanic,
            ]);

            foreach ($employeeIds as $employeeId) {
                $assignedEmployees[] = [
                    'job_order_id' => $jobOrder->id,
                    'evaluator_id' => $employeeId,
                    'evaluatee_id' => $personnel->team_leader
                ];
            }
        }

        DB::transaction(fn () => EmployeePerformance::insert($assignedEmployees));

        $employeeRatings = [];

        foreach (EmployeePerformance::all() as $performance) {
            foreach (PerformanceCategory::all() as $category) {
                $employeeRatings[] = [
                    'employee_performance_id' => $performance->id,
                    'performance_category_id' => $category->id,
                    'performance_rating_id' => PerformanceRating::inRandomOrder()->first()->id,
                ];
            }
        }

        DB::transaction(fn () => EmployeeRating::insert($employeeRatings));
    }
}
