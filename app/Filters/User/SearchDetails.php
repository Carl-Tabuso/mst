<?php

namespace App\Filters\User;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchDetails
{
    public function __construct(private ?string $search = '') {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        $query->with('employee');

        if (! $this->search) {
            return $next($query);
        }

        $query->where(function (Builder $subQuery) {
            $subQuery
                ->whereLike('email', "%{$this->search}%")
                ->orWhereHas('employee', function (Builder $ssubQuery) {
                    $ssubQuery->whereAny([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'suffix',
                    ], 'like', "%{$this->search}%")
                        ->orWhere(fn (Builder $sssubQuery) => $sssubQuery->searchName($this->search))
                        ->orWhereRaw("concat_ws(' ', first_name, middle_name, last_name, suffix) like ?", "%{$this->search}%");
                });
        });

        return $next($query);
    }
}
