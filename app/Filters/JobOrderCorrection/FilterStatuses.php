<?php

namespace App\Filters\JobOrderCorrection;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterStatuses
{
    public function __construct(private array $statusFilters) {}

    public function __invoke(Builder $query, Closure $next)
    {
        if (count($this->statusFilters) < 1) {
            return $next($query);
        }

        return $next($query->ofStatuses($this->statusFilters));
    }
}
