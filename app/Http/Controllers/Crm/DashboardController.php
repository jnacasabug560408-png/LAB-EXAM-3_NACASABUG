<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Branch;
use App\Models\Feedback;
use App\Models\Guest;
use App\Models\Interaction;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Support\CrmMetrics;
use App\Support\TenantContext;

class DashboardController extends Controller
{
    public function __construct(
        protected TenantContext $context,
        protected CrmMetrics $metrics,
    ) {}

    public function index()
    {
        if ($this->context->isGlobalView()) {
            return $this->masterDashboard();
        }

        return match ($this->context->tenant()?->code) {
            'A' => $this->tenantADashboard(),
            'B' => $this->tenantBDashboard(),
            'C' => $this->tenantCDashboard(),
            default => $this->tenantBDashboard(),
        };
    }

    protected function masterDashboard()
    {
        $stats = [
            'tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'branches' => Branch::count(),
            'reservations' => Reservation::count(),
            'guests' => Guest::count(),
            'revenue' => (float) Sale::where('status', 'completed')->sum('amount'),
            'mrr' => (float) Subscription::where('status', 'active')->sum('monthly_price'),
        ];

        $perTenant = Tenant::withCount(['reservations', 'guests'])
            ->with('subscription')
            ->orderBy('code')
            ->get()
            ->map(function (Tenant $tenant) {
                $tenant->revenue = (float) Sale::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tenant->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                return $tenant;
            });

        return view('crm.dashboards.master', compact('stats', 'perTenant'));
    }

    protected function tenantADashboard()
    {
        $summary = $this->metrics->summary();

        $todayArrivals = Reservation::with('guest')
            ->whereDate('check_in_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $inHouse = Reservation::with('guest')->where('status', 'checked_in')->get();

        $recentSales = Sale::with('guest')->latest('sold_at')->take(8)->get();
        $recentInteractions = Interaction::with('guest')->latest()->take(5)->get();

        return view('crm.dashboards.tenant-a', compact('summary', 'todayArrivals', 'inHouse', 'recentSales', 'recentInteractions'));
    }

    protected function tenantBDashboard()
    {
        $summary = $this->metrics->summary();
        $revenueTrend = $this->metrics->revenueTrend();
        $actions = Action::with('assignee', 'guest')->latest()->take(8)->get();
        $actionCounts = collect(Action::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => Action::where('status', $status)->count()])
            ->all();

        return view('crm.dashboards.tenant-b', compact('summary', 'revenueTrend', 'actions', 'actionCounts'));
    }

    protected function tenantCDashboard()
    {
        $summary = $this->metrics->summary();

        return view('crm.dashboards.tenant-c', [
            'summary' => $summary,
            'revenueTrend' => $this->metrics->revenueTrend(),
            'occupancyTrend' => $this->metrics->occupancyTrend(),
            'reservationStatuses' => $this->metrics->reservationStatusBreakdown(),
            'feedbackAnalytics' => $this->metrics->feedbackAnalytics(),
            'openActions' => Action::with('assignee')->where('status', '!=', 'Resolved')->latest()->take(8)->get(),
            'pendingPromotions' => Promotion::where('status', 'Pending')->count(),
            'latestFeedback' => Feedback::with('guest')->latest()->take(5)->get(),
        ]);
    }
}
