<?php

namespace App\Filters\JobOrderCorrection;

use App\Enums\JobOrderCorrectionRequestStatus;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterDistinctByJobOrderId
{
    public function __invoke(Builder $query, Closure $next)
    {
        $builder = $query->orderByDesc('created_at')->whereIn('id', function ($subQuery) {
            $subQuery->selectRaw('MAX(id)')
                // ->where('status', JobOrderCorrectionRequestStatus::Pending->value)
                ->groupBy('job_order_id');
        });

        return $next($builder);
    }
}
