<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\EmployeeRating;
use App\Models\Form3;
use App\Models\Form4;
use App\Models\PerformanceSummary;
use App\Models\JobOrder;
use App\Models\PerformanceCategory;
use App\Models\PerformanceRating;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeePerformanceSeeder extends Seeder
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
    
        $form3 = Form3::factory()->create(['form4_id' => $jobOrder->serviceable->id]);
    
        $position = Position::firstWhere(['name' => 'Hauler']);
        $haulers  = Employee::factory(rand(10, 12))->create(['position_id' => $position->id]);
        $form3->haulers()->attach($haulers);
    
        $employeeIds = array_merge($haulers->pluck('id')->toArray(), [
            $form3->team_driver,
            $form3->safety_officer,
            $form3->team_mechanic,
        ]);

        PerformanceSummary::factory()->create(['job_order_id' => $jobOrder->id]);
    
        $summary = PerformanceSummary::where('job_order_id', $jobOrder->id)->first();
    
        $assignedEmployees = [];
        foreach ($employeeIds as $employeeId) {
            $assignedEmployees[] = [
                'job_order_id'           => $jobOrder->id,
                'evaluator_id'           => $employeeId,
                'evaluatee_id'           => $form3->team_leader,
                'performance_summary_id' => $summary?->id, // nullable fallback
                'created_at'             => now(),
                'updated_at'             => now(),
            ];
        }
    
        DB::transaction(fn () => EmployeePerformance::insert($assignedEmployees));
    
        $employeeRatings = [];
        foreach (EmployeePerformance::all() as $performance) {
            foreach (PerformanceCategory::all() as $category) {
                $employeeRatings[] = [
                    'employee_performance_id' => $performance->id,
                    'performance_category_id' => $category->id,
                    'performance_rating_id'   => PerformanceRating::inRandomOrder()->first()->id,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];
            }
        }
    
        DB::transaction(fn () => EmployeeRating::insert($employeeRatings));
    }
}
