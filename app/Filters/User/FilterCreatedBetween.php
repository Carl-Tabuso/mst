<?php

namespace App\Filters\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class FilterCreatedBetween
{
    public function __construct(protected ?string $fromDate, protected ?string $toDate) {}

    public function handle(Builder $query, $next)
    {
        if (blank($this->fromDate) && blank($this->toDate)) {
            return $next($query);
        }

        $query->where(function (Builder $query) {
            if (! blank($this->fromDate)) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->fromDate));
            }

            if (! blank($this->toDate)) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->toDate));
            }
        });

        return $next($query);
    }
}
