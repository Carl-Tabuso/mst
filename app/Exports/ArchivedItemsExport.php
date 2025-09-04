<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArchivedItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'Record ID',
            'Record Name',
            'Display Name', 
            'Type',
            'Archived By',
            'Archived By Position',
            'Date Archived',
            'Time Archived'
        ];
    }

    public function map($item): array
    {
        // Format the archived date
        $archivedDate = '';
        $archivedTime = '';
        
        if (isset($item['archived_at']) && $item['archived_at']) {
            try {
                $date = new \DateTime($item['archived_at']);
                $archivedDate = $date->format('M d, Y');
                $archivedTime = $date->format('h:i A');
            } catch (\Exception $e) {
                $archivedDate = 'Invalid Date';
                $archivedTime = '';
            }
        }

        return [
            $item['id'] ?? '',
            $item['name'] ?? '',
            $item['display_name'] ?? '',
            $this->getTypeDisplayName($item['type'] ?? ''),
            $item['archived_by_name'] ?? '',
            $item['archived_by_position'] ?? '',
            $archivedDate,
            $archivedTime
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
        ];
    }

    private function getTypeDisplayName($type): string
    {
        return match ($type) {
            'job_order' => 'Job Order',
            'user' => 'User',
            'employee' => 'Employee',
            'correction' => 'Correction',
            'performance_rating' => 'Performance Rating',
            default => ucfirst(str_replace('_', ' ', $type))
        };
    }
}
