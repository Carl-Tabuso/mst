<?php
namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

class EmployeeHistoryRatingsExport implements
FromCollection,
WithHeadings,
WithMapping,
WithStyles,
WithTitle,
ShouldAutoSize,
WithCustomStartCell
{
    protected $employee;
    protected $ratings;
    protected $filters;
    protected $totalRecords;
    protected $originalTotalRecords;

    public function __construct($employee, Collection $ratings, $filters = [])
    {
        $this->employee     = $employee;
        $this->ratings      = $ratings;
        $this->filters      = $filters;
        $this->totalRecords = $ratings->count();

        $this->originalTotalRecords = $this->getOriginalTotalCount();
    }

    public function collection()
    {
        return $this->ratings;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'Job Order Ticket',
            'Evaluator Name',
            'Evaluator Position',
            'Department',
            'Rating Scale',
            'Rating Description',
            'Remarks/Comments',
            'Evaluation Date',
        ];
    }

    public function map($rating): array
    {
        return [
            $this->getTicketDisplay($rating),
            $rating['from'],
            $rating['from_position'],
            'Waste Management',
            $rating['scale'],
            $this->getRatingDescription($rating['scale']),
            $rating['description'] ?: 'No remarks provided',
            Carbon::parse($rating['job_datetime'])->format('F j, Y g:i A'),
        ];
    }

    /**
     * Get the proper ticket display format
     */
    private function getTicketDisplay($rating): string
    {
        // Use the ticket if available, otherwise fall back to the old format
        return $rating['job_order_ticket'] ?? $this->generateTicketFromId($rating['job_order_id']);
    }

    /**
     * Generate ticket format from ID (fallback for older records)
     */
    private function generateTicketFromId($jobOrderId): string
    {
        // Generate the ticket format similar to the JobOrder model
        $currentYear = now()->format('y');
        $paddedId    = str_pad($jobOrderId, 7, '0', STR_PAD_LEFT);
        return "JO-{$currentYear}{$paddedId}";
    }

    public function styles(Worksheet $sheet)
    {
        $this->addHeaderInformation($sheet);

        $lastDataRow = 5 + $this->totalRecords;

        return [
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

            // Subtitle styling
            2                     => [
                'font'      => [
                    'size'  => 12,
                    'bold'  => true,
                    'color' => ['rgb' => '374151'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            3                     => [
                'font'      => [
                    'size'   => 10,
                    'italic' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            4                     => [
                'font'      => [
                    'size' => 9,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            6                     => [
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

            'A7:H' . $lastDataRow => [
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
            'A:A'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Job Order Ticket
            'E:E'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Rating Scale
            'F:F'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Rating Description
            'H:H'                 => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]], // Evaluation Date
        ];
    }

    private function addHeaderInformation(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', "Performance History: {$this->employee->full_name}");
        $sheet->mergeCells('A1:H1');

        $sheet->setCellValue('A2', "Position: {$this->employee->position->name} | Department: Waste Management");
        $sheet->mergeCells('A2:H2');

        $filterInfo = $this->generateFilterInfo();
        $sheet->setCellValue('A3', $filterInfo);
        $sheet->mergeCells('A3:H3');

        $summaryInfo = $this->generateSummaryInfo();
        $sheet->setCellValue('A4', $summaryInfo);
        $sheet->mergeCells('A4:H4');

        $sheet->setCellValue('A5', '');
        $sheet->mergeCells('A5:H5');
    }

    private function generateFilterInfo()
    {
        $filterParts = [];

        if ($this->filters['scale_from'] !== null || $this->filters['scale_to'] !== null) {
            $from          = $this->filters['scale_from'] ?? 'Min';
            $to            = $this->filters['scale_to'] ?? 'Max';
            $filterParts[] = "Rating Range: {$from} - {$to}";
        }

        if (! empty($this->filters['date_from']) || ! empty($this->filters['date_to'])) {
            $from = $this->filters['date_from']
            ? Carbon::parse($this->filters['date_from'])->format('M j, Y')
            : 'Earliest';
            $to = $this->filters['date_to']
            ? Carbon::parse($this->filters['date_to'])->format('M j, Y')
            : 'Latest';
            $filterParts[] = "Date Range: {$from} - {$to}";
        }

        if (! empty($this->filters['sort'])) {
            $sortMap = [
                'date_asc'   => 'Date (Oldest First)',
                'date_desc'  => 'Date (Newest First)',
                'scale_asc'  => 'Rating (Low to High)',
                'scale_desc' => 'Rating (High to Low)',
            ];
            $filterParts[] = 'Sort: ' . ($sortMap[$this->filters['sort']] ?? $this->filters['sort']);
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
        // Get total count of all ratings for this employee
        return $this->employee->performancesAsEmployee
            ->filter(fn($perf) => $perf->deleted_at === null)
            ->flatMap(fn($perf) => $perf->ratings)
            ->filter(fn($rating) => $rating->performanceRating?->scale !== null)
            ->count();
    }

    private function getRatingDescription($scale)
    {
        $rating = (float) $scale;

        if ($rating >= 4.5) {
            return 'Excellent';
        }

        if ($rating >= 4.0) {
            return 'Very Good';
        }

        if ($rating >= 3.5) {
            return 'Good';
        }

        if ($rating >= 3.0) {
            return 'Satisfactory';
        }

        if ($rating >= 2.5) {
            return 'Fair';
        }

        if ($rating >= 2.0) {
            return 'Needs Improvement';
        }

        return 'Poor';
    }

    public function title(): string
    {
        return 'Performance History';
    }
}
