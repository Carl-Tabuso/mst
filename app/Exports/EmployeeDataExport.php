<?php

namespace App\Exports;

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

class EmployeeDataExport implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $employees;
    protected $filters;
    protected $totalRecords;
    protected $originalTotalRecords;

    public function __construct($employees = null, $filters = [])
    {
        $this->employees = $employees;
        $this->filters = $filters;
        $this->totalRecords = $employees ? $employees->count() : 0;
        $this->originalTotalRecords = $this->getOriginalTotalCount();
    }

    public function collection()
    {
        return $this->employees ?: collect([]);
    }

    public function startCell(): string
    {
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
            'Total Job Orders',
            'Job Orders Created',
            'Completed Created Job Orders',
            'Corrections Made',
            'Total Errors',
            'Success Rate (Created)',
            'Rating Status',
            'Export Date',
        ];
    }

    public function map($employee): array
    {
        $ratingStatus = $employee->has_ratings ? 'Has Ratings' : 'No Ratings Yet';
        $completionRate = $employee->completion_rate > 0
            ? $employee->completion_rate . '%'
            : 'N/A';

        $successRateCreated = $employee->success_rate_created > 0
            ? $employee->success_rate_created . '%'
            : 'N/A';

        return [
            $employee->id,
            $employee->full_name,
            $employee->position,
            $employee->average_rating ?? 'N/A',
            $employee->total_ratings,
            $employee->total_job_orders,
            $employee->job_orders_created,
            $employee->completed_job_orders_created,
            $employee->corrections_made,
            $employee->total_errors,
            $successRateCreated,
            $ratingStatus,
            now()->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $this->addHeaderInformation($sheet);

        $lastDataRow = 4 + $this->totalRecords;

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            2 => [
                'font' => [
                    'size' => 10,
                    'italic' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            3 => [
                'font' => [
                    'size' => 9,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            5 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            'A6:P' . $lastDataRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'A:A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'D:D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'E:E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'F:F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'G:G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'H:H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'I:I' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'J:J' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'K:K' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'L:L' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'M:M' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'N:N' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'O:O' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'P:P' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    private function addHeaderInformation(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', 'Employee Performance & Job Order Statistics Export');
        $sheet->mergeCells('A1:P1');

        $filterInfo = $this->generateFilterInfo();
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->mergeCells('A2:P2');

        $summaryInfo = $this->generateSummaryInfo();
        $sheet->setCellValue('A3', $summaryInfo);
        $sheet->mergeCells('A3:P3');

        $sheet->setCellValue('A4', '');
        $sheet->mergeCells('A4:P4');
    }

    private function generateFilterInfo()
    {
        $filterParts = [];

        if (!empty($this->filters['positions'])) {
            $filterParts[] = 'Positions: ' . implode(', ', $this->filters['positions']);
        }

        if (!empty($this->filters['search'])) {
            $filterParts[] = 'Search: "' . $this->filters['search'] . '"';
        }

        return empty($filterParts)
            ? 'Filters: All Performance Roles (Driver, Hauler, Team Leader, Frontliner)'
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
        $allowedPositions = ['Driver', 'Hauler', 'Team Leader', 'Frontliner'];
        return \App\Models\Employee::whereHas('position', fn($q) => $q->whereIn('name', $allowedPositions))->count();
    }

    public function title(): string
    {
        return 'Employee Performance & Job Orders';
    }
}
