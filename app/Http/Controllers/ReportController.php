<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\AnnualReportService;

class ReportController extends Controller
{
    public function __construct(private AnnualReportService $service) {}

    public function index(Request $request): Response
    {
        $year = $request->integer('year', 2024);

        $data = $this->service->processAnnualReport($year);

        return Inertia::render('reports/Index', compact('data'));
    }
}
