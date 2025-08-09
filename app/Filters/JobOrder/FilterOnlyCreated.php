<?php

namespace App\Filters\JobOrder;

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

        return $next($query->where('created_by', $this->user->employee->id));
    }
}
