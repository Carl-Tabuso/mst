<?php

namespace App\Services;

use App\Filters\JobOrder\ApplyDateOfServiceRange;
use App\Filters\JobOrder\FilterOnlyArchived;
use App\Filters\JobOrder\FilterOnlyChecklist;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\FilterStatuses;
use App\Filters\JobOrder\SearchDetails;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Pipeline;

class JobOrderService
{
    public function getAllJobOrders(?int $perPage = 10, ?string $search = '', ?array $filters = [], bool $archivedOnly = false): mixed
    {
        $user = request()->user();

        $pipes = [
            ...($archivedOnly ? [new FilterOnlyArchived] : []),
            new FilterOnlyCreated($user),
            new FilterOnlyChecklist($user),
            new FilterStatuses($filters),
            new ApplyDateOfServiceRange($filters),
            new SearchDetails($search),
        ];

        return Pipeline::send(JobOrder::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedOnly) {
                return $query->with('creator')
                    ->latest($archivedOnly ? new JobOrder()->getDeletedAtColumn() : 'created_at')
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function restoreArchivedJobOrder(JobOrder $jobOrder)
    {
        return $jobOrder->restore();
    }

    public function restoreArchivedJobOrders(array $jobOrders)
    {
        //
    }
}
