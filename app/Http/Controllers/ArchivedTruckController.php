<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Services\TruckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchivedTruckController extends Controller
{
    public function __construct(private TruckService $truckService) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->truckService->getAllTrucks($perPage, $search, $filters, true);

        return Inertia::render('archives/trucks/Index', compact('data'));
    }

    public function update(Truck $truck): RedirectResponse
    {
        $this->truckService->restoreArchivedTruck($truck);

        $message = __('responses.restore.truck', [
            'model'    => $truck->model,
            'plate_no' => $truck->plate_no,
        ]);

        return back()->with(compact('message'));
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $truckIds = $request->input('truckIds', []);

        $this->truckService->bulkRestoreArchivedTrucks($truckIds);

        $message = __('responses.batch_restore.truck', ['count' => count($truckIds)]);

        return back()->with(compact('message'));
    }

    public function destroy(Truck $truck): RedirectResponse
    {
        $this->truckService->permanentlyDeleteTruck($truck);

        $message = __('responses.permanent_delete.truck', [
            'model'    => $truck->model,
            'plate_no' => $truck->plate_no,
        ]);

        return back()->with(compact('message'));
    }
}
