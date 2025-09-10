<?php

namespace App\Filters\JobOrder;

use App\Models\JobOrder;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ApplyDateOfArchivalRange
{
    public function __construct(private array $filters = []) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $filters = (object) $this->filters;

        $hasDateOfArchivalRange =
            isset($filters->fromDateOfArchived, $filters->toDateOfArchived) &&
            ($filters->fromDateOfArchived && $filters->toDateOfArchived);

        if (! $hasDateOfArchivalRange) {
            return $next($query);
        }

        $range = [
            Carbon::parse($filters->fromDateOfArchived)->startOfDay(),
            Carbon::parse($filters->toDateOfArchived)->endOfDay(),
        ];

        $archivedAtColumn = new JobOrder()->getDeletedAtColumn();

        $builder = $query->where(fn (Builder $subQuery) => $subQuery->whereBetween($archivedAtColumn, $range));

        return $next($builder);
    }
}
