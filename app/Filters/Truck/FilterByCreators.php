<?php

namespace App\Filters\Truck;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByCreators
{
    public function __construct(private array $filters = []) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $filters = (object) $this->filters;

        $hasCreatorsFilter = isset($filters->dispatchers) && count($filters->dispatchers) > 0;

        if (! $hasCreatorsFilter) {
            return $next($query);
        }

        return $next($query->whereIn('added_by', $filters->dispatchers));
    }
}
