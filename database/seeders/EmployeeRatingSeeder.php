<?php
namespace Database\Seeders;

use App\Models\EmployeePerformance;
use App\Models\EmployeeRating;
use App\Models\PerformanceCategory;
use App\Models\PerformanceRating;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeRatingSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRatings = [];

        $ratings = PerformanceRating::all();

        foreach (EmployeePerformance::all() as $performance) {
            foreach (PerformanceCategory::all() as $category) {
                $employeeRatings[] = [
                    'employee_performance_id' => $performance->id,
                    'performance_category_id' => $category->id,
                    'performance_rating_id'   => $ratings->random()->id,
                    'description'             => fake()->optional(0.7)->sentence(rand(5, 12)), 
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];
            }
        }

        DB::transaction(fn() => EmployeeRating::insert($employeeRatings));
    }
}
