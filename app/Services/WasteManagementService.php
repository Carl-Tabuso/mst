<?php

namespace App\Services;

use App\Enums\HaulingStatus;
use App\Enums\JobOrderServiceType;
use App\Enums\JobOrderStatus;
use App\Filters\JobOrder\ApplyDateOfServiceRange;
use App\Filters\JobOrder\FilterOnlyChecklist;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\FilterServiceType;
use App\Filters\JobOrder\FilterStatuses;
use App\Filters\JobOrder\SearchDetails;
use App\Http\Resources\JobOrderResource;
use App\Models\Employee;
use App\Models\Form3AssignedPersonnel;
use App\Models\Form3Hauling;
use App\Models\Form3HaulingChecklist;
use App\Models\Form4;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Inertia\Inertia;

class WasteManagementService
{
    public function getAllWasteManagementJobOrders(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $user = request()->user();

        $pipes = [
            new FilterServiceType(JobOrderServiceType::Form4),
            new FilterOnlyChecklist($user),
            new FilterOnlyCreated($user),
            new FilterStatuses($filters),
            new ApplyDateOfServiceRange($filters),
            new SearchDetails($search),
        ];

        return Pipeline::send(JobOrder::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage) {
                return $query->with('creator')
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function storeWasteManagement(array $validated): JobOrder
    {
        return DB::transaction(function () use ($validated) {
            $wasteManagement = Form4::create();

            return $wasteManagement->jobOrder()->create($validated);
        });
    }

    public function getWasteManagementData(JobOrder $ticket): array
    {
        $loads = $ticket->load([
            'creator'     => ['account:avatar'],
            'cancel',
            'corrections',
            'serviceable' => [
                'dispatcher' => ['account:avatar'],
                'appraisers' => ['account:avatar'],
                'form3'      => [
                    'haulings' => [
                        'checklist',
                        'haulers'           => ['account:avatar'],
                        'assignedPersonnel' => [
                            'teamLeader'    => ['account:avatar'],
                            'teamDriver'    => ['account:avatar'],
                            'safetyOfficer' => ['account:avatar'],
                            'teamMechanic'  => ['account:avatar'],
                        ],
                    ],
                ],
            ],
        ]);

        $jobOrder = JobOrderResource::make($loads);

        $employees = Inertia::optional(
            fn () => Employee::with('account:avatar')->get()->toResourceCollection()
        );

        return compact('jobOrder', 'employees');
    }

    public function updateWasteManagement(array $validated, Form4 $form4): string
    {
        $status = JobOrderStatus::from($validated['status']);

        return match ($status) {
            JobOrderStatus::ForAppraisal      => $this->handleForAppraisal($form4, $validated),
            JobOrderStatus::Successful        => $this->handleSuccessful($form4, $validated),
            JobOrderStatus::PreHauling        => $this->handlePrehauling($form4, $validated),
            JobOrderStatus::InProgress        => $this->handleInProgress($form4, $validated),
        };
    }

    private function handleForAppraisal(Form4 $form4, array $data): string
    {
        DB::transaction(function () use ($form4, $data) {
            $form4->update(['form_dispatcher' => $data['user']->id]);

            $form4->form3()->create(['appraised_date' => Carbon::parse($data['appraised_date'])]);

            $form4->jobOrder()->update(['status' => JobOrderStatus::ForViewing]);

            $appraisers = array_map(fn ($appraiser) => $appraiser['id'], $data['appraisers']);
            $form4->appraisers()->sync($appraisers);
        });

        return JobOrderStatus::ForViewing->value;
    }

    private function handleSuccessful(Form4 $form4, array $data): string
    {
        DB::transaction(function () use ($form4, $data) {
            $form4->form3()->update([
                'payment_type'  => $data['payment_type'],
                'approved_date' => Carbon::parse($data['approved_date']),
            ]);

            $form4->update([
                'payment_date' => Carbon::parse($data['payment_date']),
                'or_number'    => $data['or_number'],
                'bid_bond'     => $data['bid_bond'],
            ]);

            $form4->jobOrder()->update([
                'status' => JobOrderStatus::PreHauling,
            ]);
        });

        return JobOrderStatus::PreHauling->value;
    }

    private function handlePrehauling(Form4 $form4, array $data): string
    {
        DB::transaction(function () use ($form4, $data) {
            $from = Carbon::parse($data['from']);
            $to   = Carbon::parse($data['to']);

            $form4->form3()->update([
                'from' => $from,
                'to'   => $to,
            ]);

            $duration = (int) $from->diffInDays($to) + 1;

            $haulingInserts = array_map(fn ($range) => [
                'form3_id' => $form4->form3->id,
                'date'     => $from->copy()->addDays($range),
            ], range(0, $duration - 1));

            Form3Hauling::insert($haulingInserts);

            $preHaulingInserts = $form4->form3->haulings->map(fn ($hauling) => [
                'form3_hauling_id' => $hauling->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ])->toArray();

            Form3HaulingChecklist::insert($preHaulingInserts);

            Form3AssignedPersonnel::insert($preHaulingInserts);

            $form4->jobOrder()->update([
                'status' => JobOrderStatus::InProgress,
            ]);
        });

        return JobOrderStatus::InProgress->value;
    }

    private function handleInProgress(Form4 $form4, array $data)
    {
        $filteredHaulings = array_filter($data['haulings'],
            fn ($haul) => Carbon::parse($haul['date'])->gte(today())
        );

        $mappedHaulings = array_map(function ($hauling) {
            $personnel = $hauling['assignedPersonnel'];

            return [
                'id'                => $hauling['id'],
                'assignedPersonnel' => [
                    'team_leader'    => $personnel['teamLeader']['id']    ?? null,
                    'team_driver'    => $personnel['teamDriver']['id']    ?? null,
                    'safety_officer' => $personnel['safetyOfficer']['id'] ?? null,
                    'team_mechanic'  => $personnel['teamMechanic']['id']  ?? null,
                ],
                'haulers'  => array_map(fn ($h) => $h['id'], $hauling['haulers']),
                'truck_no' => $hauling['truckNo'] ?? null,
            ];
        }, $filteredHaulings);

        $form4->form3->haulings()
            ->where('date', '>=', today())
            ->get()
            ->each(function ($hauling) use ($mappedHaulings) {
                $mapped = array_find($mappedHaulings, fn ($mh) => $hauling->id === $mh['id']);

                $personnel                      = $mapped['assignedPersonnel'];
                $isForSafetyInspectionChecklist =
                    $mapped['truck_no']          &&
                    $personnel['team_leader']    &&
                    $personnel['team_driver']    &&
                    $personnel['safety_officer'] &&
                    ! empty($mapped['haulers']);

                DB::transaction(function () use ($hauling, $mapped, $isForSafetyInspectionChecklist) {
                    $hauling->assignedPersonnel()->update($mapped['assignedPersonnel']);

                    empty($mapped['haulers'])
                        ? $hauling->haulers()->detach()
                        : $hauling->haulers()->sync($mapped['haulers']);

                    $hauling->update([
                        'truck_no' => $mapped['truck_no'],
                        'status'   => $isForSafetyInspectionChecklist
                                        ? HaulingStatus::ForSafetyInspection
                                        : HaulingStatus::ForPersonnelAssignment,
                    ]);
                });
            });
    }
}
