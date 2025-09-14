<?php

namespace App\Services;

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
use App\Models\Form5;
use App\Models\Form5Item;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Inertia\Inertia;

class Form5Service
{
    public function getAllForm5JobOrders(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $user = request()->user();

        $pipes = [
            new FilterServiceType(JobOrderServiceType::Form5),
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

    public function storeForm5(array $validated): JobOrder
    {
        return DB::transaction(function () use ($validated) {
            $form5 = Form5::create([
                'assigned_person'  => $validated['assigned_person'] ?? null,
                'purpose'          => $validated['purpose']         ?? null,
            ]);

            if (isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    Form5Item::create([
                        'form5_id'  => $form5->id,
                        'item_name' => $itemData['item_name'],
                        'quantity'  => $itemData['quantity'],
                    ]);
                }
            }

            $jobOrderData = array_merge($validated, ['status' => JobOrderStatus::InProgress]);

            return $form5->jobOrder()->create($jobOrderData);
        });
    }

    public function getForm5Data(JobOrder $ticket): array
    {
        $loads = $ticket->load([
            'creator' => ['account:avatar'],
            'cancel',
            'corrections',
            'serviceable' => [
                'assignedPerson' => ['account:avatar'],
                'items',
            ],
        ]);

        $jobOrder = JobOrderResource::make($loads);

        $employees = Inertia::optional(
            fn () => Employee::with('account:avatar')->get()
        );

        return compact('jobOrder', 'employees');
    }

    public function updateForm5(array $validated, Form5 $form5): string
    {
        return DB::transaction(function () use ($validated, $form5) {
            if (isset($validated['assigned_person'])) {
                $form5->update(['assigned_person' => $validated['assigned_person']]);
            }

            if (isset($validated['items']) && is_array($validated['items'])) {
                $form5->items()->delete();

                foreach ($validated['items'] as $itemData) {
                    Form5Item::create([
                        'form5_id'  => $form5->id,
                        'item_name' => $itemData['item_name'],
                        'quantity'  => $itemData['quantity'],
                    ]);
                }
            }

            if (isset($validated['status'])) {
                $form5->jobOrder()->update(['status' => JobOrderStatus::from($validated['status'])]);
            }

            return $form5->jobOrder->fresh()->status->value;
        });
    }

    public function markAsCompleted(Form5 $form5): string
    {
        return DB::transaction(function () use ($form5) {
            $form5->jobOrder()->update(['status' => JobOrderStatus::Completed]);

            return JobOrderStatus::Completed->value;
        });
    }

    public function markAsInProgress(Form5 $form5): string
    {
        return DB::transaction(function () use ($form5) {
            $form5->jobOrder()->update(['status' => JobOrderStatus::InProgress]);

            return JobOrderStatus::InProgress->value;
        });
    }
}
