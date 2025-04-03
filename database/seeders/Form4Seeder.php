<?php

namespace Database\Seeders;

use App\Models\Form4;
use App\Models\Employee;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class Form4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobOrder::factory()
            ->for(Form4::factory(), 'serviceable')
            ->create()
            ->each(function ($jobOrder) {
                $jobOrder->serviceable->appraisers()->attach(
                    Employee::inRandomOrder()->take(rand(2, 4))->get()
                );
            });
    }
}
