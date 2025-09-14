<?php

namespace App\Filters\Employee;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterStatuses
{
    public function __construct(private array $statuses) {}

    public function __invoke(Builder $query, Closure $next)
    {
        if (empty($this->statuses)) {
            return $next($query);
        }

        $query->where(function ($q) {
            if (in_array('no_account', $this->statuses, true)) {
                $q->orWhereDoesntHave('account');
            }
            if (in_array('active', $this->statuses, true)) {
                $q->orWhereHas('account', fn ($q) => $q->whereNull('deleted_at'));
            }
            if (in_array('deactivated', $this->statuses, true)) {
                $q->orWhereHas('account', fn ($q) => $q->whereNotNull('deleted_at'));
            }
        });

        return $next($query);
    }
}
