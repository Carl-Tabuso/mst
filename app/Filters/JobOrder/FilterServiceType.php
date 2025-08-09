<?php

namespace App\Filters\JobOrder;

use App\Enums\JobOrderServiceType;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterServiceType
{
    public function __construct(private JobOrderServiceType $serviceType) {}

    public function __invoke(Builder $query, Closure $next)
    {
        return $next($query->ofServiceType($this->serviceType));
    }
}
