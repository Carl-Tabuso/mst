<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class JobOrderController extends Controller
{
    const PER_PAGE = 10;

    public function index(Request $request): Response
    {
        // dd($request);
        $perPage = $request->input('per_page', self::PER_PAGE);

        [
            'search' => $search,
            'statuses' => $statuses,
            'fromDos' => $fromDos, 
            'toDos' => $toDos
        ] = $request;

        $hasStatuses = $statuses ? count($statuses) > 0 : null;

        $jobOrders = JobOrder::query()
            ->when($hasStatuses, fn ($q) => $q->ofStatuses($statuses))
            ->when($fromDos && $toDos, function ($q) use($fromDos, $toDos) {
                return $q->whereBetween('date_time', [
                    Carbon::parse($fromDos)->startOfDay(),
                    Carbon::parse($toDos)->endOfDay()
                ]);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->whereLike('client', $search)
                        ->orWhereLike('address', $search)
                        ->orWhereLike('contact_no', $search)
                        ->orWhereLike('contact_person', $search)
                        ->orWhereHas('creator', function ($subQuery) use ($search) {
                            return $subQuery->whereLike('first_name', $search)
                                ->orWhereLike('middle_name', $search)
                                ->orWhereLike('last_name', $search)
                                ->orWhereLike('suffix', $search);
                        });
                });
            })
            ->with(['creator'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->toResourceCollection();

        return Inertia::render('job-orders/Index', compact('jobOrders'));
    }

    public function create(): Response
    {
        return Inertia::render('job-orders/Create');
    }

    public function store(Request $request)
    {
        //
    }
}
