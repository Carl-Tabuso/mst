<?php

namespace App\Exports;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TruckExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Model',
            'Plate Number',
            'Added By',
            'Added At',
            'Updated At',
        ];
    }

    public function collection(): Collection
    {
        $truckCollection = collect();

        Truck::query()
            ->with('creator')
            ->chunk(100, function (EloquentCollection $trucks) use ($truckCollection) {
                $truckCollection->push($this->mapNecessaryValuesOnly($trucks));
            });

        return $truckCollection;
    }

    private function mapNecessaryValuesOnly(EloquentCollection $trucks): Collection
    {
        return $trucks->map(fn (Truck $truck) => [
            $truck->model,
            $truck->plate_no,
            $truck->creator?->full_name,
            $truck->created_at,
            $truck->updated_at,
        ]);
    }
}
