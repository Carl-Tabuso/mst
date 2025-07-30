<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Jenssegers\Agent\Facades\Agent;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::query()
            ->latest()
            ->get()
            ->groupBy(fn ($activity) => $activity->created_at->toDateString())
            ->map(fn ($group, $date) => [
                'date'  => $date,
                'items' => $group->map(fn ($log) => [
                    'title'       => $log->properties['title'],
                    'time'        => $log->created_at->format('g:i A'),
                    'log'         => $log->log_name,
                    'description' => $log->description,
                    'ipAddress'   => $log->properties['ip_address'],
                    'browser'     => Agent::browser($log->properties['user_agent']),
                    'platform'    => Agent::platform($log->properties['user_agent']),
                ])
            ])->values();

            // dd($activities);


        return Inertia::render('activity/Index', [
            'activityLogs' => $activities
        ]);
    }
}
