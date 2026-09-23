<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Support\CrmMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    public const PERIODS = ['daily', 'weekly', 'monthly'];

    public function __construct(protected CrmMetrics $metrics) {}

    public function index(Request $request)
    {
        $period = in_array($request->string('period')->toString(), self::PERIODS, true)
            ? $request->string('period')->toString()
            : 'daily';

        [$from, $to] = $this->range($period);

        $sales = Sale::with('guest', 'branch', 'recorder')
            ->whereBetween('sold_at', [$from, $to])
            ->latest('sold_at')
            ->get();

        $completed = $sales->where('status', 'completed');

        $summary = [
            'revenue' => (float) $completed->sum('amount'),
            'completed_orders' => $completed->count(),
            'transactions' => $sales->count(),
            'average_order' => $completed->count() ? round($completed->sum('amount') / $completed->count(), 2) : 0.0,
            'refunded' => (float) $sales->where('status', 'refunded')->sum('amount'),
        ];

        $breakdown = $completed
            ->groupBy(fn (Sale $sale) => $this->metrics->bucketLabel($period, $sale->sold_at))
            ->map(fn ($group) => [
                'orders' => $group->count(),
                'revenue' => (float) $group->sum('amount'),
            ]);

        return view('crm.reports.sales', [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'summary' => $summary,
            'breakdown' => $breakdown,
            'chart' => $this->metrics->salesSeries($period, $from, $to),
            'byCategory' => $completed->groupBy('category')->map(fn ($g) => (float) $g->sum('amount')),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function range(string $period): array
    {
        $to = now()->endOfDay();

        $from = match ($period) {
            'weekly' => now()->subWeeks(11)->startOfWeek(),
            'monthly' => now()->subMonths(11)->startOfMonth(),
            default => now()->subDays(13)->startOfDay(),
        };

        return [$from, $to];
    }
}
