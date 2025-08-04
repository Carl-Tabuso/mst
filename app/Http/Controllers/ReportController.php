<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        /**
         * use current year as default
         * [
         *  jobOrderMetrics => [
         *      January => 4
         *      February => 5
         *      March => 3
         *      .
         *      .
         *      .
         *  ],
         *  top => [
         *      client => MST Technologies
         *      month => January
         *      frontliner => [
         *          John Doe,
         *          Carl Tabuso,
         *          Sed Redondo,
         *          .
         *          .
         *          .
         *      ]
         *  ]
         * ]
         */

        $data = collect([
            'metrics' => collect(),
            'top' => collect([
                'client' => null,
                'month' => '',
                'frontliners' => collect()
            ])
        ]);

        // score  = total job orders created - total corrections made
        
        JobOrder::query()
            ->withTrashed()
            ->whereYear('created_at', $request->integer('year', now()->year))
            ->chunk(100, function (Collection $jobOrders) use ($data) {
                $groupedByMonths = $jobOrders->groupBy(fn ($jobOrder) => $jobOrder->created_at->monthName);
                
                $data['metrics'];
            });

        dd(
            $data,
        );

        return Inertia::render('reports/Index');
    }
}
