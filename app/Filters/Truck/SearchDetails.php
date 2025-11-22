<?php

namespace App\Filters\Truck;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchDetails
{
    public function __construct(private ?string $searchQuery = '') {}

    public function __invoke(Builder $query, Closure $next): mixed
    {
        if (! $this->searchQuery) {
            return $next($query);
        }

        $query->where(fn (Builder $subQuery) => $subQuery->whereAny([
            'model',
            'plate_no',
        ], 'like', "%{$this->searchQuery}%"));

        return $next($query);
    }
}
