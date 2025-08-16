<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $service) {}

    public function index(Request $request): Response
    {
        $activities = $this->service->getGroupedByDateLogs($request->integer('page', 10));

        return Inertia::render('activity/Index', [
            'activityLogs' => $activities,
        ]);
    }
}
