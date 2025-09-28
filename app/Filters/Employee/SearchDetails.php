<?php

namespace App\Filters\Employee;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchDetails
{
    public function __construct(private ?string $search) {}

    public function __invoke(Builder $query, Closure $next)
    {
        if (empty($this->search)) {
            return $next($query);
        }

        $query->where(function (Builder $subQuery) {
            $subQuery
                ->whereAny(['email', 'contact_number'], 'like', "%{$this->search}%")
                ->orWhere(fn (Builder $ssubQuery) => $ssubQuery->searchName($this->search))
                ->orWhereHas('position', fn (Builder $ssubQuery) => $ssubQuery->whereLike('name', "%{$this->search}%"));
        });

        return $next($query);
    }
}
