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
                ->whereAny([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                    'email',
                    'contact_number',
                ], 'like', "%{$this->search}%")
                ->orWhereHas('position', fn (Builder $ssubQuery) => $ssubQuery->whereLike('name', "%{$this->search}%"))
                ->orWhereRaw("concat_ws(' ', first_name, middle_name, last_name, suffix) like ?", "%{$this->search}%");
        });

        return $next($query);
    }
}
