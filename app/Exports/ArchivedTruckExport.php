<?php

namespace App\Exports;

use App\Models\Truck;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArchivedTruckExport implements FromCollection, WithHeadings
{
    public function __construct(private array $truckIds = []) {}

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
        return Truck::query()
            ->withTrashed()
            ->whereIn('id', $this->truckIds)
            ->get()
            ->map(fn (Truck $truck) => [
                $truck->model,
                $truck->plate_no,
                $truck->creator?->full_name,
                $truck->created_at,
                $truck->updated_at,
            ]);
    }
}
