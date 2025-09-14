<?php

namespace App\Filters\Employee;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterCreatedBetween
{
    public function __construct(private ?string $from, private ?string $to) {}

    public function __invoke(Builder $query, Closure $next)
    {
        if ($this->from) {
            $query->whereHas('account', fn ($q) => $q->whereDate('created_at', '>=', $this->from));
        }

        if ($this->to) {
            $query->whereHas('account', fn ($q) => $q->whereDate('created_at', '<=', $this->to));
        }

        return $next($query);
    }
}
