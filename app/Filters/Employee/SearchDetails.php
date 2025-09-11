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

        $query->where(function ($q) {
            $q->where('first_name', 'like', "%{$this->search}%")
              ->orWhere('last_name', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%")
              ->orWhere('contact_number', 'like', "%{$this->search}%")
              ->orWhereHas('position', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        });

        return $next($query);
    }
}
