<?php

namespace App\Filters\JobOrderCorrection;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterOnlyCreated
{
    public function __construct(private User $user) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $isFrontliner = $this->user->hasRole(UserRole::Frontliner);

        if (! $isFrontliner) {
            return $next($query);
        }

        $builder = $query->whereHas('jobOrder', function (Builder $subQuery) {
            $subQuery->where('created_by', $this->user->employee_id);
        });

        return $next($builder);
    }
}
