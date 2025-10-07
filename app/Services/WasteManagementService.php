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
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\JobOrderResource;
use App\Models\Employee;
use App\Models\Form3;
use App\Models\Form3AssignedPersonnel;
use App\Models\Form3Hauling;
use App\Models\Form3HaulingChecklist;
use App\Models\Form4;
use App\Models\Incident;
use App\Models\JobOrder;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

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

    public function getWasteManagementData(JobOrder $ticket): JobOrderResource
    {
        $loads = $ticket->load([
            'creator'     => ['account'],
            'cancel',
            'corrections',
            'serviceable' => [
                'dispatcher' => ['account'],
                'appraisers' => ['account'],
                'form3'      => [
                    'haulings' => [
                        'checklist',
                        'trucks',
                        'drivers',
                        'haulers'           => ['account'],
                        'assignedPersonnel' => [
                            'teamLeader'    => ['account'],
                            'teamDriver'    => ['account'],
                            'safetyOfficer' => ['account'],
                            'teamMechanic'  => ['account'],
                        ],
                    ],
                ],
            ],
        ]);

        return JobOrderResource::make($loads);
    }

    public function getAllTrucks(): ResourceCollection
    {
        return Truck::all()->toResourceCollection();
    }

    public function getEmployeesMappedByAccountRole(): Collection
    {
        return Employee::query()
            ->with('account.roles')
            ->has('account')
            ->get()
            ->groupBy(fn (Employee $employee) => $employee->account->getRoleNames()->first())
            ->map(fn (Collection $grouped, string $role) => [
                'role'  => $role,
                'items' => EmployeeResource::collection($grouped),
            ])
            ->values();
    }

    public function updateWasteManagement(array $validated, Form4 $form4): mixed
    {
        $status = JobOrderStatus::from($validated['status']);

        return match ($status) {
            JobOrderStatus::Successful => $this->handleSuccessful($form4, $validated),
            JobOrderStatus::PreHauling => $this->handlePrehauling($form4, $validated),
            JobOrderStatus::InProgress => $this->handleInProgress($form4, $validated),
        };
    }

    public function handleForAppraisal(Form4 $form4, array $data): string
    {
        DB::transaction(function () use ($form4, $data) {
            $this->updateOrCreateAppraisalInformation($form4, $data);

            $form4->update(['form_dispatcher' => $data['employeeId']]);

            $form4->jobOrder()->update(['status' => JobOrderStatus::ForViewing]);
        });

        return JobOrderStatus::ForViewing->value;
    }

    public function updateOrCreateAppraisalInformation(Form4 $form4, array $data): void
    {
        DB::transaction(function () use ($form4, $data) {
            Form3::updateOrCreate(
                ['form4_id' => $form4->id],
                ['appraised_date' => Carbon::parse($data['appraised_date'])],
            );

            $appraisers = array_map(fn ($appraiser) => $appraiser['id'], $data['appraisers']);

            $form4->appraisers()->sync($appraisers);
        });
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

            $incidentInserts = $form4->form3->haulings->map(function ($hauling) {
                return [
                    'form3_hauling_id' => $hauling->id,
                    'created_by'       => null,
                    'status'           => \App\Enums\IncidentStatus::Draft->value,
                    'subject'          => 'Incident Report for Hauling '.$hauling->date->format('Y-m-d'),
                    'location'         => 'To be determined',
                    'infraction_type'  => 'To be determined',
                    'occured_at'       => now(),
                    'description'      => 'Auto-generated incident report for hauling operation',
                    'is_read'          => false,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            })->toArray();

            Incident::insert($incidentInserts);

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

    private function handleInProgress(Form4 $form4, array $data): void
    {
        $filteredHaulings = array_filter(
            $data['haulings'],
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
                'drivers'  => array_map(fn ($driver) => $driver['id'], $hauling['drivers']),
                'haulers'  => array_map(fn ($hauler) => $hauler['id'], $hauling['haulers']),
                'truck_id' => $hauling['truck']['id'] ?? null,
                'trucks'   => array_map(fn ($truck) => $truck['id'], $hauling['trucks']),
            ];
        }, $filteredHaulings);

        $form4->form3->haulings()
            ->where('date', '>=', today())
            ->get()
            ->each(function ($hauling) use ($mappedHaulings) {
                $mapped = array_find($mappedHaulings, fn ($mh) => $hauling->id === $mh['id']);

                $personnel                      = $mapped['assignedPersonnel'];
                $isForSafetyInspectionChecklist =
                    $personnel['team_leader']    &&
                    $personnel['team_driver']    &&
                    $personnel['safety_officer'] &&
                    ! empty($mapped['trucks'])   &&
                    ! empty($mapped['drivers'])  &&
                    ! empty($mapped['haulers']);

                DB::transaction(function () use ($hauling, $mapped, $isForSafetyInspectionChecklist) {
                    $hauling->assignedPersonnel()->update($mapped['assignedPersonnel']);

                    empty($mapped['haulers'])
                        ? $hauling->haulers()->detach()
                        : $hauling->haulers()->sync($mapped['haulers']);

                    empty($mapped['drivers'])
                        ? $hauling->drivers()->detach()
                        : $hauling->drivers()->sync($mapped['drivers']);

                    empty($mapped['trucks'])
                        ? $hauling->trucks()->detach()
                        : $hauling->trucks()->sync($mapped['trucks']);

                    $hauling->update([
                        'status' => $isForSafetyInspectionChecklist
                            ? HaulingStatus::ForSafetyInspection
                            : HaulingStatus::ForPersonnelAssignment,
                    ]);
                });
            });
    }
}
