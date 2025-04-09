<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamLeaderRating extends Pivot
{
    protected $table = 'team_leader_ratings';

    public function performance(): BelongsTo
    {
        return $this->belongsTo(TeamLeaderPerformance::class);
    }
}
