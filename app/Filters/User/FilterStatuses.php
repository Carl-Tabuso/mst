<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;

class FilterStatuses
{
    public function __construct(protected array $statuses = []) {}

    public function handle(Builder $query, $next)
    {
        if (empty($this->statuses)) {
            return $next($query);
        }

        $query->where(function (Builder $query) {
            if (in_array('active', $this->statuses)) {
                $query->orWhereNull('deleted_at');
            }

            if (in_array('inactive', $this->statuses)) {
                $query->orWhereNotNull('deleted_at');
            }
        });

        return $next($query);
    }
}
