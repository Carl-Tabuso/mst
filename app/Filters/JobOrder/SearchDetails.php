<?php

namespace App\Filters\JobOrder;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchDetails
{
    public function __construct(private ?string $searchQuery = '') {}

    public function __invoke(Builder $query, Closure $next)
    {
        $builder = $query->when($this->searchQuery, function (Builder $query) {
            $query->where(function (Builder $subQuery) {
                $subQuery
                    ->whereAny([
                        'client',
                        'address',
                        'contact_no',
                        'contact_person',
                    ], 'like', "%{$this->searchQuery}%")
                    ->orWhereHas('creator', fn (Builder $ssubQuery) => $ssubQuery->whereAny([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'suffix',
                    ], 'like', "%{$this->searchQuery}%"));
            });
        });

        return $next($builder);
    }
}
