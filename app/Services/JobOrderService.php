<?php

namespace App\Services;

use App\Filters\FilterOnlyArchived;
use App\Filters\JobOrder\ApplyDateOfArchivalRange;
use App\Filters\JobOrder\ApplyDateOfServiceRange;
use App\Filters\JobOrder\FilterOnlyChecklist;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\FilterStatuses;
use App\Filters\JobOrder\SearchDetails;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
            new ApplyDateOfArchivalRange($filters),
        ];

        return Pipeline::send(JobOrder::query())
            ->through($pipes)
            ->then(function (Builder $query) use ($perPage, $archivedOnly) {
                return $query->with('creator.account')
                    ->latest($archivedOnly ? (new JobOrder)->getDeletedAtColumn() : 'created_at')
                    ->paginate($perPage)
                    ->withQueryString()
                    ->toResourceCollection();
            });
    }

    public function restoreArchivedJobOrder(JobOrder $jobOrder): bool
    {
        return $jobOrder->restore();
    }

    public function restoreArchivedJobOrders(array $jobOrderIds): mixed
    {
        return JobOrder::query()
            ->onlyTrashed()
            ->whereIn('id', $jobOrderIds)
            ->restore();
    }

    public function archiveJobOrder(JobOrder $jobOrder): ?bool
    {
        return $jobOrder->delete();
    }

    public function archiveJobOrders(array $jobOrderIds): mixed
    {
        return DB::transaction(fn () => JobOrder::destroy($jobOrderIds));
    }

    public function permanentlyDeleteJobOrder(JobOrder $jobOrder): mixed
    {
        return DB::transaction(function () use ($jobOrder) {
            $jobOrder->forceDelete();
            $jobOrder->serviceable()->forceDelete();

            return true;
        });
    }
}
