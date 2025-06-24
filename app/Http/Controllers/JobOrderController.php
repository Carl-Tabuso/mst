<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class JobOrderController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', self::PER_PAGE);

        $search = "%{$request->input('search')}%";

        $filters = null;

        if ($request->has('filters')) {
            $filters = (object) $request->array('filters');
        }

        $hasStatuses = isset($filters->statuses) && count($filters->statuses) > 0;

        $hasDateOfServiceRange =
            isset($filters->fromDateOfService, $filters->toDateOfService) &&
            $filters?->fromDateOfService                                  && $filters?->toDateOfService;

        $jobOrders = JobOrder::query()
            ->when($hasStatuses, fn ($q) => $q->ofStatuses($filters->statuses))
            ->when($hasDateOfServiceRange, function ($q) use ($filters) {
                return $q->whereBetween('date_time', [
                    Carbon::parse($filters->fromDateOfService)->startOfDay(),
                    Carbon::parse($filters->toDateOfService)->endOfDay(),
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

    public function show(JobOrder $jobOrder)
    {
        //
    }

    public function edit(JobOrder $jobOrder)
    {
        //
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        //
    }

    public function destroy(JobOrder $jobOrder)
    {
        //
    }
}
