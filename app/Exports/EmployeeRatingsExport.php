<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeRatingsExport implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filteredData;

    protected $filters;

    protected $totalRecords;

    protected $originalTotalRecords;

    public function __construct($filteredData = null, $filters = [])
    {
        $this->filteredData = $filteredData;
        $this->filters      = $filters;
        $this->totalRecords = $filteredData ? $filteredData->count() : 0;

        // Get original total for comparison
        $this->originalTotalRecords = $this->getOriginalTotalCount();
    }

    public function collection()
    {
        // Always use the pre-filtered data from controller
        // This ensures export matches exactly what user sees in table
        if ($this->filteredData) {
            return $this->filteredData;
        }

        // Fallback - should rarely be used since we're passing data from controller
        return collect([]);
    }

    public function startCell(): string
    {
        // Start data from row 5 to leave space for title, filter info, and summary
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Full Name',
            'Position',
            'Average Rating',
            'Total Ratings',
            'Job Orders Attended',
            'Rating Status',
            'Export Date',
        ];
    }

    public function map($employee): array
    {
        $ratingStatus = $employee->has_ratings ? 'Has Ratings' : 'No Ratings Yet';

        return [
            $employee->id,
            $employee->full_name,
            $employee->position,
            $employee->average_rating ?? 'N/A',
            $employee->total_ratings,
            $employee->job_orders_attended ?? 0,
            $ratingStatus,
            now()->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add title, filter information, and summary
        $this->addHeaderInformation($sheet);

        $lastDataRow = 4 + $this->totalRecords;

        return [
            // Title styling
            1                     => [
                'font'      => [
                    'bold'  => true,
                    'size'  => 16,
                    'color' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Filter info styling
            2                     => [
                'font'      => [
                    'size'   => 10,
                    'italic' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Summary styling
            3                     => [
                'font'      => [
                    'size' => 9,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Header row styling
            5                     => [
                'font'      => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill'      => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],

            // Data rows styling
            'A6:I' . $lastDataRow => [
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],

            // Center align specific columns
            'A:A'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Employee ID
            'E:E'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Average Rating
            'F:F'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Total Ratings
            'G:G'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Job Orders Attended
            'H:H'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Rating Status
            'I:I'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Export Date
        ];
    }

    private function addHeaderInformation(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', 'Employee Performance Ratings Export');
        $sheet->mergeCells('A1:I1');

        $filterInfo = $this->generateFilterInfo();
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->mergeCells('A2:I2');

        $summaryInfo = $this->generateSummaryInfo();
        $sheet->setCellValue('A3', $summaryInfo);
        $sheet->mergeCells('A3:I3');

        $sheet->setCellValue('A4', '');
        $sheet->mergeCells('A4:I4');

        $sheet->getStyle('A3')->applyFromArray([
            'font'      => [
                'size' => 9,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
    }

    private function generateFilterInfo()
    {
        $filterParts = [];

        if (! empty($this->filters['positions'])) {
            $filterParts[] = 'Positions: '.implode(', ', $this->filters['positions']);
        }

        if (! empty($this->filters['evaluation_status'])) {
            $statusMap = [
                'has_ratings' => 'Has Ratings',
                'no_ratings'  => 'No Ratings',
            ];
            $statuses      = array_map(fn ($s) => $statusMap[$s] ?? $s, $this->filters['evaluation_status']);
            $filterParts[] = 'Status: '.implode(', ', $statuses);
        }

        if ($this->filters['rating_from'] !== null || $this->filters['rating_to'] !== null) {
            $from          = $this->filters['rating_from'] ?? 'Min';
            $to            = $this->filters['rating_to']   ?? 'Max';
            $filterParts[] = "Rating Range: {$from} - {$to}";
        }

        if (! empty($this->filters['search'])) {
            $filterParts[] = 'Search: "'.$this->filters['search'].'"';
        }

        if (! empty($this->filters['sort'])) {
            $sortMap = [
                'name_asc'    => 'Name (A-Z)',
                'name_desc'   => 'Name (Z-A)',
                'rating_asc'  => 'Rating (Low to High)',
                'rating_desc' => 'Rating (High to Low)',
            ];
            $filterParts[] = 'Sort: '.($sortMap[$this->filters['sort']] ?? $this->filters['sort']);
        }

        return empty($filterParts)
            ? 'Filters: All Records'
            : 'Applied Filters: ' . implode(' | ', $filterParts);
    }

    private function generateSummaryInfo()
    {
        $exportTime = now()->format('F j, Y g:i A');

        if ($this->totalRecords === $this->originalTotalRecords) {
            return "Exported Records: {$this->totalRecords} (All Records) | Generated: {$exportTime}";
        }

        return "Exported Records: {$this->totalRecords} of {$this->originalTotalRecords} (Filtered) | Generated: {$exportTime}";
    }

    private function getOriginalTotalCount()
    {
        $allowedPositions = ['Team Leader', 'Driver', 'Hauler'];

        return Employee::whereHas('position', fn ($q) => $q->whereIn('name', $allowedPositions))->count();
    }

    public function title(): string
    {
        return 'Employee Ratings';
    }
}
