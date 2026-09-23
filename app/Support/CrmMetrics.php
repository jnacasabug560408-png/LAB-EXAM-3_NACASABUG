<?php

namespace App\Support;

use App\Models\Action;
use App\Models\Branch;
use App\Models\Feedback;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Sale;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class CrmMetrics
{
    public function __construct(protected TenantContext $context) {}

    /**
     * Headline KPIs shared by every business-intelligence dashboard.
     */
    public function summary(): array
    {
        return [
            'total_guests' => Guest::count(),
            'active_guests' => Guest::where('status', 'active')->count(),
            'total_reservations' => Reservation::count(),
            'active_reservations' => Reservation::active()->count(),
            'occupancy_rate' => $this->occupancyRate(),
            'revenue' => (float) Sale::where('status', 'completed')->sum('amount'),
            'revenue_this_month' => (float) Sale::where('status', 'completed')
                ->where('sold_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'unresolved_feedback' => Feedback::where('status', 'unresolved')->count(),
            'open_actions' => Action::where('status', '!=', 'Resolved')->count(),
            'average_rating' => round((float) Feedback::avg('rating'), 2),
        ];
    }

    public function occupancyRate(): float
    {
        $rooms = (int) Branch::sum('total_rooms');

        if ($rooms === 0) {
            return 0.0;
        }

        $occupied = Reservation::where('status', 'checked_in')->count();

        return round(min(100, ($occupied / $rooms) * 100), 1);
    }

    /**
     * Daily revenue for the last $days days, ready for a chart.
     */
    public function revenueTrend(int $days = 14): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Sale::where('status', 'completed')
            ->where('sold_at', '>=', $start)
            ->get(['sold_at', 'amount'])
            ->groupBy(fn (Sale $sale) => $sale->sold_at->toDateString())
            ->map(fn ($group) => (float) $group->sum('amount'));

        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($start, now()->endOfDay()) as $day) {
            $labels[] = $day->format('M d');
            $values[] = $rows->get($day->toDateString(), 0.0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function occupancyTrend(int $days = 14): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $rooms = max(1, (int) Branch::sum('total_rooms'));

        $reservations = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->where('check_out_date', '>=', $start)
            ->get(['check_in_date', 'check_out_date']);

        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($start, now()->endOfDay()) as $day) {
            $labels[] = $day->format('M d');
            $occupied = $reservations->filter(
                fn (Reservation $r) => $r->check_in_date <= $day && $r->check_out_date >= $day
            )->count();
            $values[] = round(min(100, ($occupied / $rooms) * 100), 1);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function reservationStatusBreakdown(): array
    {
        $counts = Reservation::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $breakdown = [];

        foreach (Reservation::STATUSES as $status) {
            $breakdown[$status] = (int) $counts->get($status, 0);
        }

        return $breakdown;
    }

    public function feedbackAnalytics(): array
    {
        $ratings = Feedback::selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return [
            'by_rating' => collect(range(1, 5))
                ->mapWithKeys(fn ($rating) => [$rating => (int) $ratings->get($rating, 0)])
                ->all(),
            'unresolved' => Feedback::where('status', 'unresolved')->count(),
            'resolved' => Feedback::where('status', 'resolved')->count(),
            'average' => round((float) Feedback::avg('rating'), 2),
        ];
    }

    /**
     * Sales grouped by day, week or month for the sales report module.
     */
    public function salesSeries(string $period, Carbon $from, Carbon $to): array
    {
        $sales = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to])
            ->get(['sold_at', 'amount']);

        $grouped = $sales->groupBy(fn (Sale $sale) => $this->bucketLabel($period, $sale->sold_at));

        $labels = [];
        $values = [];

        foreach ($this->buckets($period, $from, $to) as $label) {
            $labels[] = $label;
            $values[] = (float) ($grouped->get($label)?->sum('amount') ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function bucketLabel(string $period, Carbon $date): string
    {
        return match ($period) {
            'weekly' => 'Week '.$date->copy()->startOfWeek()->format('W (M d)'),
            'monthly' => $date->format('M Y'),
            default => $date->format('M d, Y'),
        };
    }

    protected function buckets(string $period, Carbon $from, Carbon $to): array
    {
        $interval = match ($period) {
            'weekly' => '1 week',
            'monthly' => '1 month',
            default => '1 day',
        };

        $cursor = match ($period) {
            'weekly' => $from->copy()->startOfWeek(),
            'monthly' => $from->copy()->startOfMonth(),
            default => $from->copy()->startOfDay(),
        };

        $labels = [];

        foreach (CarbonPeriod::create($cursor, $interval, $to) as $date) {
            $labels[] = $this->bucketLabel($period, $date);
        }

        return array_values(array_unique($labels));
    }
}
