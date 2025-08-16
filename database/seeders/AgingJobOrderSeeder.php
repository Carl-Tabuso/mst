<?php

namespace Database\Seeders;

use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AgingJobOrderSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        DB::transaction(function () {
            $jobOrders = JobOrder::factory()
                ->count(10)
                ->for(Form4::factory(), 'serviceable')
                ->status(JobOrderStatus::ForProposal)
                ->create([
                    'date_time'  => $servicedAt = Carbon::parse(fake()->dateTimeBetween('-4 weeks', '-2 weeks')),
                    'created_at' => $created    = $servicedAt,
                    'updated_at' => $created->copy()->addWeek(),
                ]);

            $jobOrders->each(function ($jobOrder) {
                $jobOrder->serviceable->appraisers()->attach(
                    Employee::inRandomOrder()->take(rand(2, 4))->get()
                );
            });
        });
    }
}
