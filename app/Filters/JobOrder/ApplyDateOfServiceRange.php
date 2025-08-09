<?php

namespace App\Filters\JobOrder;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ApplyDateOfServiceRange
{
    public function __construct(private array $filters) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $filters = (object) $this->filters;

        $hasDateOfServiceRange =
            isset($filters->fromDateOfService, $filters->toDateOfService) &&
            ($filters->fromDateOfService && $filters->toDateOfService);

        if (! $hasDateOfServiceRange) {
            return $next($query);
        }

        $range = [
            Carbon::parse($filters->fromDateOfService)->startOfDay(),
            Carbon::parse($filters->toDateOfService)->endOfDay(),
        ];

        $builder = $query->when($hasDateOfServiceRange, function (Builder $query) use ($range) {
            $query->where(fn (Builder $subQuery) => $subQuery->whereBetween('date_time', $range));
        });

        return $next($builder);
    }
}
