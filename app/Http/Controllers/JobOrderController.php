<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobOrderController extends Controller
{
    public function index(Request $request): Response
    {
        // $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $search = "%{$request->search}%";

        $jobOrders = JobOrder::query()
            ->when($search, fn ($query) =>
                $query->whereLike('client', $search)
                    ->orWhereLike('address', $search)
                    ->orWhereLike('contact_no', $search)
                    ->orWhereLike('contact_person', $search)
                    ->orWhereLike('status', $search)
                    ->orWhereHas('creator', fn ($subQuery) =>
                        $subQuery->whereLike('first_name', $search)
                            ->orWhereLike('middle_name', $search)
                            ->orWhereLike('last_name', $search)
                            ->orWhereLike('suffix', $search)
                    )
            )
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
}
