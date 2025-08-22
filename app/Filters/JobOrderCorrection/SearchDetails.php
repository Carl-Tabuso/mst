<?php

namespace App\Filters\JobOrderCorrection;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchDetails
{
    public function __construct(private ?string $searchQuery = '') {}

    public function __invoke(Builder $query, Closure $next)
    {
        if (! $this->searchQuery) {
            return $next($query);
        }

        $builder = $query->where(function (Builder $subQuery) {
            $subQuery
                ->whereHas('jobOrder.creator', function (Builder $ssubQuery) {
                    $ssubQuery->whereAny([
                        'first_name',
                        'middle_name',
                        'last_name',
                        'suffix',
                    ], 'like', "%{$this->searchQuery}%");
                })
                ->orWhereHas('jobOrder', function (Builder $ssubQuery) {
                    $format = 'JO-'.now()->format('y');
                    $id     = (int) str_replace($format, '', $this->searchQuery);
                    $ssubQuery->where('id', $id)->orWhereLike('id', "%{$this->searchQuery}%");
                })
                ->orWhereLike('reason', "%{$this->searchQuery}%");
        });

        return $next($builder);
    }
}
