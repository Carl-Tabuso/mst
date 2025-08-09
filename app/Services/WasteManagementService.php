<?php

namespace App\Services;

use App\Enums\JobOrderServiceType;
use App\Filters\JobOrder\FilterServiceType;
use App\Models\JobOrder;
use App\Filters\JobOrder\SearchDetails;
use App\Filters\JobOrder\FilterStatuses;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\JobOrder\FilterOnlyCreated;
use App\Filters\JobOrder\ApplyDateOfServiceRange;

class WasteManagementService
{
    public function getAllWasteManagementJobOrders(?int $perPage = 10, ?string $search = '', ?array $filters = [])
    {
        $pipes = [
            new FilterServiceType(JobOrderServiceType::Form4),
            new FilterOnlyCreated(request()->user()),
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
}
