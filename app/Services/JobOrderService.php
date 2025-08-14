<?php

namespace App\Services;

use App\Enums\JobOrderStatus;
use App\Filters\JobOrder\ApplyDateOfServiceRange;
use App\Filters\JobOrder\FilterOnlyChecklist;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\FilterStatuses;
use App\Filters\JobOrder\SearchDetails;
use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Pipeline;

class JobOrderService
{
    public function getAllJobOrders(?int $perPage = 10, ?string $search = '', ?array $filters = []): mixed
    {
        $user = request()->user();

        $pipes = [
            new FilterOnlyCreated($user),
            new FilterOnlyChecklist($user),
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

    public function getAgingJobOrders()
    {
        $x = JobOrder::query()
            ->latest()
            ->where('status', JobOrderStatus::ForProposal)
            ->where('updated_at', '<=', now()->subWeek())
            ->take(10)
            ->get();

        dd($x);
    }
}
