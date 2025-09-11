<?php

namespace App\Filters\JobOrder;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterOnlyArchived
{
    public function __invoke(Builder $query, Closure $next)
    {
        return $next($query->onlyTrashed());
    }
}
