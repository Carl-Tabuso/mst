<?php

namespace App\Filters\JobOrder;

use App\Enums\UserPermission;
use App\Models\Form4;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterOnlyChecklist
{
    public function __construct(private User $user) {}

    public function __invoke(Builder $query, Closure $next)
    {
        $isTeamLeader = $this->user->hasPermissionTo(UserPermission::FillOutSafetyInspectionChecklist);

        if (! $isTeamLeader) {
            return $next($query);
        }

        $builder = $query->whereHasMorph('serviceable', [Form4::class], function (Builder $query) {
            $query->whereHas('form3.haulings.assignedPersonnel', function (Builder $subQuery) {
                $subQuery->where('team_leader', $this->user->employee->id);
            });
        });

        return $next($builder);
    }
}
