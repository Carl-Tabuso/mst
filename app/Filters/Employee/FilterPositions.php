<?php

namespace App\Filters\Employee;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterPositions
{
    public function __construct(private array $filters = []) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $filters = (object) $this->filters;

        $hasPositions = isset($filters->positions) && count($filters->positions) > 0;

        if (! $hasPositions) {
            return $next($query);
        }

        $query->whereHas('position', function (Builder $subQuery) use ($filters) {
            $subQuery->whereIn('id', $filters->positions);
        });

        return $next($query);
    }
}
