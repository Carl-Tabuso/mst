<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreTruckRequest;
use App\Http\Requests\UpdateTruckRequest;
use App\Models\Employee;
use App\Models\Truck;
use App\Services\TruckService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TruckController extends Controller
{
    public function __construct(private TruckService $truckService) {}

    public function index(Request $request): Response
    {
        $searchQuery = $request->input('search', '');

        $perPage = $request->integer('per_page', 15);

        $filters = $request->input('filters', []);

        $data = $this->truckService->getAllTrucks($perPage, $searchQuery, $filters);

        $data->onEachSide(1);

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
        $validated = $request->validated();

        if (! $truck->added_by) {
            $validated['added_by'] = $request->user()->employee_id;
        }

        $updated = tap($truck)->update($validated);

        $message = __('responses.truck.update', [
            'model'    => $updated->model,
            'plate_no' => $updated->plate_no,
        ]);

        return back()->with(compact('message'));
    }

    public function destroy(Truck $truck): RedirectResponse
    {
        $this->truckService->archiveTruck($truck);

        $message = __('responses.truck.archive', [
            'model'    => $truck->model,
            'plate_no' => $truck->plate_no,
        ]);

        return back()->with(compact('message'));
    }
}
