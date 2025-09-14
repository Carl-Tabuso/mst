<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ApplyDateOfArchivalRange
{
    public function __construct(
        private string $deletedAtColumn = 'deleted_at',
        private array $filters = [],
    ) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        $filters = (object) $this->filters;

        $hasDateOfArchivalRange =
            isset($filters->fromDateArchived, $filters->toDateArchived) &&
            ($filters->fromDateArchived && $filters->toDateArchived);

        if (! $hasDateOfArchivalRange) {
            return $next($query);
        }

        $range = [
            Carbon::parse($filters->fromDateArchived)->startOfDay(),
            Carbon::parse($filters->toDateArchived)->endOfDay(),
        ];

        $query->where(fn (Builder $subQuery) => $subQuery->whereBetween($this->deletedAtColumn, $range));

        return $next($query);
    }
}
