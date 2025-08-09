<?php

namespace App\Filters\JobOrder;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterStatuses
{
    public function __construct(private array $filters) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $filters = (object) $this->filters;

        $hasStatuses = isset($filters->statuses) && count($filters->statuses) > 0;

        if (! $hasStatuses) {
            return $next($query);
        }

        return $next($query->ofStatuses($filters->statuses));
    }
}
