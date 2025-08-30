<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Truck;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Resources\TruckResource;
use Illuminate\Database\Eloquent\Builder;

class TruckController extends Controller
{
    public function index(Request $request): Response
    {
        $searchQuery = $request->input('search', '');

        $trucks = Truck::query()
            ->with('creator')
            ->where(fn (Builder $query) => $query->whereAny([
                'model',
                'plate_no',
            ], 'like', "%{$searchQuery}%"))
            ->paginate()
            ->withQueryString();

        $data = TruckResource::collection($trucks);

        return Inertia::render('trucks/Index', compact('data'));
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
