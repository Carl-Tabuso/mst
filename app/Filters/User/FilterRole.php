<?php

namespace App\Filters\User;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterRole
{
    public function __construct(private ?array $filters = []) {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        $filters = (object) $this->filters;

        $hasRoleFilter = isset($filters->roles) && count($filters->roles) > 0;

        $query->with('roles');

        if (! $hasRoleFilter) {
            return $next($query);
        }

        $query->whereHas('roles', function (Builder $subQuery) use ($filters) {
            $subQuery->whereIn('name', $filters->roles);
        });

        return $next($query);
    }
}
