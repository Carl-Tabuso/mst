<?php

namespace Database\Seeders;

use App\Enums\HaulingStatus;
use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\JobOrder;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class Form3HaulingSeeder extends Seeder
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
            Employee::inRandomOrder()->take(mt_rand(2, 4))->get()
        );

        $form3 = Form3::factory()->create([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => $from = Carbon::parse(fake()->dateTimeBetween('-2 months', '-3 weeks')),
            'to'       => $to   = $from->copy()->addDays(6),
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

        $haulings->each(fn ($hauling) => $hauling->haulers()->attach($haulers));
    }
}
