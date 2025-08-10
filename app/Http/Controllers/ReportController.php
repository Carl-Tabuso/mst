<?php

namespace App\Http\Controllers;

use App\Services\AnnualReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(private AnnualReportService $service) {}

    public function index(Request $request): Response
    {
        $year = $request->integer('year', now()->year);

        $data = Inertia::optional(fn () => $this->service->processAnnualReport($year));

        $availableYears = Inertia::optional(fn () => $this->service->getAvailableYears());

        return Inertia::render('reports/Index', compact('data', 'availableYears'));
    }
}
