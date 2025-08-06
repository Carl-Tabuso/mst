<?php

namespace Database\Seeders;

use App\Enums\HaulingStatus;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3Hauling;
use App\Models\Form4;
use App\Models\Form5;
use App\Models\ITService;
use App\Models\JobOrder;
use App\Models\PerformanceCategory;
use App\Models\Position;
use App\Traits\RandomEmployee;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnnualReportSeeder extends Seeder
{
    use RandomEmployee;

    const FAKE_COMPANIES = [
        'Horizon Tech Solutions',
        'Apex Global Enterprises',
        'Silverline Logistics Co.',
        'NovaWave Systems',
        'Summit Edge Industries',
        'Quantum Core Technologies',
        'StellarWorks Manufacturing',
        'BrightPath Consulting Group',
        'BluePeak Innovations',
        'IronGate Security Solutions',
        'Evergreen Energy Corp.',
        'MetroLink Communications',
        'PrimePoint Financial Services',
        'SkyBridge Engineering Ltd.',
        'UrbanVista Developers',
        'RedOak Healthcare Partners',
        'OceanReach Shipping Lines',
        'SwiftStone Construction Inc.',
        'GoldenLeaf Foods Ltd.',
        'NextEra Digital Media Group',
    ];

    private static $dateCreated;

    private $categories;

    public function __construct()
    {
        $this->categories = PerformanceCategory::all();
    }

    public function run(): void
    {
        DB::disableQueryLog();

        activity()->disableLogging();

        DB::transaction(function () {
            $this->seed2022();
            $this->seed2023();
            $this->seed2024();
        });

        DB::enableQueryLog();
    }

    private function seed2022(): void
    {
        $this->seedTableFor(2022);
    }

    private function seed2023(): void
    {
        $this->seedTableFor(2023);

    }

    private function seed2024(): void
    {
        $this->seedTableFor(2024);
    }

    private function seedTableFor(int $year): void
    {
        for ($month = 1; $month <= 12; $month++) {
            $jobOrderCount = mt_rand(3, 11);

            for ($j = $jobOrderCount; $j > 0; $j--) {
                $date = Carbon::create($year, $month, $j);

                $timestamps = [
                    'created_at' => $date,
                    'updated_at' => $date,
                ];

                $jobOrder = JobOrder::factory()
                                    ->for($this->getRandomService($timestamps), 'serviceable')
                                    ->status(fake()->randomElement([
                                        JobOrderStatus::Completed,
                                        JobOrderStatus::Closed,
                                    ]))
                                    ->create(array_merge($timestamps, [
                                        'client' => fake()->randomElement(self::FAKE_COMPANIES),
                                    ]));

                match ($jobOrder->serviceable_type) {
                    JobOrderServiceType::Form4->value => $this->processWasteManagement($jobOrder, $timestamps),
                    JobOrderServiceType::ITService->value => $this->processItService($jobOrder, $timestamps),
                    JobOrderServiceType::Form5->value => $this->processOtherService($jobOrder, $timestamps),
                };

                // to make sure the entire operation doesn't blow up
                unset($jobOrder);
            }
        }
    }

    private function getRandomService($timestamps): mixed
    {
        $services = [
            Form4::factory()->state($timestamps),
            Form5::factory()->state($timestamps),
            ITService::factory()->state($timestamps),
        ];

        return fake()->randomElement($services);
    }

    private function processWasteManagement(JobOrder $jobOrder, array $timestamps)
    {
        $jobOrder->serviceable->appraisers()->attach(Employee::inRandomOrder()->take(rand(2, 4))->get());

        $dateCreated = $timestamps['created_at'];

        $form3 = Form3::factory()->create(array_merge([
            'form4_id' => $jobOrder->serviceable->id,
            'from'     => $from = $dateCreated,
            'to'       => $to   = $from->copy()->addDays(4),
        ], $timestamps));

        $haulings = Form3Hauling::factory()
            ->count((int) $from->diffInDays($to) + 1)
            ->sequence(fn (Sequence $sequence) => ['date' => $from->copy()->addDays($sequence->index)])
            ->status(HaulingStatus::Done)
            ->create(['form3_id' => $form3->id]);

        // $assignedEmployees = [];

        foreach ($haulings as $hauling) {
            $personnel = $hauling->assignedPersonnel()->create(array_merge([
                'team_leader'    => $this->getByPosition('Team Leader')->id,
                'team_driver'    => $this->getByPosition('Driver')->id,
                'safety_officer' => $this->getByPosition('Safety Officer')->id,
                'team_mechanic'  => $this->getByPosition('Mechanic')->id,
            ], $timestamps));

            $position = Position::firstWhere(['name' => 'Hauler']);
            $haulers  = Employee::query()
                ->where('position_id', $position->id)
                ->inRandomOrder()
                ->take(mt_rand(10, 12))
                ->get();
            $hauling->haulers()->attach($haulers);
            $hauling->checklist()->create($timestamps)->checkAllFields();

            //     $employeeIds = array_merge($haulers->pluck('id')->toArray(), [
            //         $personnel->team_driver,
            //         $personnel->safety_officer,
            //         $personnel->team_mechanic,
            //     ]);

            //     $summary = PerformanceSummary::factory()->create(['job_order_id' => $jobOrder->id]);

            //     foreach ($employeeIds as $employeeId) {
            //         $assignedEmployees[] = [
            //             'job_order_id'           => $jobOrder->id,
            //             'evaluator_id'           => $employeeId,
            //             'evaluatee_id'           => $personnel->team_leader,
            //             'performance_summary_id' => $summary?->id,
            //             'created_at'             => now(),
            //             'updated_at'             => now(),
            //         ];
            //     }
        }

        // EmployeePerformance::insert($assignedEmployees);

        // $performances = EmployeePerformance::all();

        // $employeeRatings = [];

        // foreach ($performances as $performance) {
        //     foreach ($this->categories as $category) {
        //         $employeeRatings[] = [
        //             'employee_performance_id' => $performance->id,
        //             'performance_category_id' => $category->id,
        //             'performance_rating_id'   => PerformanceRating::inRandomOrder()->first()->id,
        //             'created_at'              => now(),
        //             'updated_at'              => now(),
        //         ];
        //     }
        // }

        // EmployeeRating::insert($employeeRatings);

        unset(
            $form3,
            $haulings,
            $haulers,
            $personnel,
            // $assignedEmployees,
            // $employeeRatings
        );
    }

    private function processItService(JobOrder $jobOrder, array $timestamps)
    {
        // whatever
    }

    private function processOtherService(JobOrder $jobOrder, array $timestamps)
    {
        // whatever
    }
}
