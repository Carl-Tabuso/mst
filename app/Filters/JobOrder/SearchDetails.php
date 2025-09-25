<?php

namespace App\Filters\JobOrder;

use App\Models\JobOrder;
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
                    ->orWhereHas('creator', fn (Builder $ssubQuery) => $ssubQuery->searchName($this->searchQuery));

                $ticketFormat   = (new JobOrder)->ticketPrefix;
                $isTicketSearch = str_contains($this->searchQuery, $ticketFormat);
                if ($isTicketSearch) {
                    $actualJobOrderId = (int) str_replace($ticketFormat, '', $this->searchQuery);
                    $subQuery->orWhere('id', $actualJobOrderId);
                }
            });
        });

        return $next($builder);
    }
}
