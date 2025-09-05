<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreTruckRequest;
use App\Http\Requests\UpdateTruckRequest;
use App\Models\Employee;
use App\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TruckController extends Controller
{
    public function index(Request $request): Response
    {
        $searchQuery = $request->input('search', '');

        $perPage = $request->integer('per_page', 15);

        $dispatcherFilters = $request->input('filters.dispatchers', []);

        $data = Truck::query()
            ->with('creator')
            ->when($searchQuery, function (Builder $query) use ($searchQuery) {
                $query->where(fn (Builder $subQuery) => $subQuery->whereAny([
                    'model',
                    'plate_no',
                ], 'like', "%{$searchQuery}%"));
            })
            ->when(count($dispatcherFilters) > 0,
                fn (Builder $query) => $query->whereIn('added_by', $dispatcherFilters)
            )
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString()
            ->toResourceCollection();

        $dispatchers = Inertia::optional(function () {
            return Employee::query()
                ->with('account.roles')
                ->whereHas('account', fn (Builder $query) => $query->role(UserRole::Dispatcher))
                ->get()
                ->toResourceCollection();
        });

        return Inertia::render('trucks/Index', compact('data', 'dispatchers'));
    }

    public function store(StoreTruckRequest $request): RedirectResponse
    {
        $data = array_merge($request->validated(), [
            'added_by' => $request->added_by,
        ]);

        Truck::create($data);

        return back()->with(['message' => __('responses.truck.create')]);
    }

    public function update(UpdateTruckRequest $request, Truck $truck): RedirectResponse
    {
        $updated = tap($truck)->update($request->validated());

        $message = __('responses.truck.update', [
            'model'    => $updated->model,
            'plate_no' => $updated->plate_no,
        ]);

        return back()->with(compact('message'));
    }
}
