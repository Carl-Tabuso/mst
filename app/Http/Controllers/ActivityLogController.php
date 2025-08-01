<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $activities = tap(
            Activity::query()
                ->with([
                    'causer' => ['employee', 'roles'],
                ])
                ->latest()
                ->paginate(10, page: $request->integer('page')),

            fn (LengthAwarePaginator $paginator) => $paginator->setCollection(
                collect($paginator->items())
                    ->groupBy(fn (Activity $activity) => $activity->created_at->toDateString())
                    ->map(fn (Collection $group, string $date) => [
                        'date'  => $date,
                        'items' => ActivityLogResource::collection($group),
                    ])->values()
            )->toArray()
        );
        // dd($activities);

        return Inertia::render('activity/Index', [
            'activityLogs' => $activities,
        ]);
    }
}
