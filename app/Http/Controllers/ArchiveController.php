<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchiveController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage        = $request->input('per_page', 10);
        $search         = $request->input('search');
        $searchWildcard = "%{$search}%";

        $archivedJobOrders = JobOrder::onlyTrashed()
            ->when($search, function ($query) use ($searchWildcard) {
                $query->where(function ($subQuery) use ($searchWildcard) {
                    $subQuery->whereLike('client', $searchWildcard)
                        ->orWhereLike('address', $searchWildcard)
                        ->orWhereLike('contact_no', $searchWildcard)
                        ->orWhereLike('contact_person', $searchWildcard)
                        ->orWhereHas('creator', function ($q) use ($searchWildcard) {
                            $q->whereLike('first_name', $searchWildcard)
                                ->orWhereLike('middle_name', $searchWildcard)
                                ->orWhereLike('last_name', $searchWildcard)
                                ->orWhereLike('suffix', $searchWildcard);
                        });
                });
            })
            ->with(['creator', 'serviceable'])
            ->latest('deleted_at')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('archive/page/Index', [
            'archivedJobOrders' => $archivedJobOrders,
            'search'            => $search,
        ]);
    }

    public function restore(Request $request)
    {
        $ids = $request->input('ids', []);
        JobOrder::onlyTrashed()->whereIn('id', $ids)->restore();

        return back()->with('success', 'Job orders restored.');
    }

    public function forceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        JobOrder::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return back()->with('success', 'Job orders permanently deleted.');
    }
}
