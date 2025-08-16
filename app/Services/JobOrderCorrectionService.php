<?php

namespace App\Services;

use App\Models\JobOrderCorrection;
use Illuminate\Database\Eloquent\Builder;

class JobOrderCorrectionService
{
    public function getAllJobOrderCorrections(?int $perPage = 10, ?string $search = '', ?array $filters = [])
    {
        return JobOrderCorrection::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->whereHas('jobOrder.creator', function (Builder $ssubQuery) use ($search) {
                            $ssubQuery->whereAny([
                                'first_name',
                                'middle_name',
                                'last_name',
                                'suffix',
                            ], 'like', "%{$search}%");
                        })
                        ->orWhereHas('jobOrder', function (Builder $ssubQuery) use ($search) {
                            $format = 'JO-'.now()->format('y');
                            $id     = (int) str_replace($format, '', $search);
                            $ssubQuery->where('id', $id)
                                ->orWhereLike('id', "%{$search}%");
                        })
                        ->orWhere('reason', 'like', "%{$search}%");
                });
            })
            ->with([
                'jobOrder' => [
                    'creator' => [
                        'account:avatar',
                    ],
                ],
            ])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();
    }
}
