<?php

namespace App\Console\Commands;

use App\Enums\HaulingStatus;
use App\Models\Form3Hauling;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class MarkHaulingAsDone extends Command
{
    protected $signature = 'hauling:mark-as-done';

    protected $description = 'Set status to (if not) Done if hauling date is past today.';

    public function handle(): void
    {
        Form3Hauling::query()
            ->whereYear('date', today())
            ->whereBeforeToday('date')
            ->whereNot('status', HaulingStatus::Done)
            ->chunk(100, function (Collection $haulings) {
                $haulings->each(fn (Form3Hauling $hauling) => $hauling->markAsDone());
            });
    }
}
