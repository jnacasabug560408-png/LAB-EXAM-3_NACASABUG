@extends('layouts.crm')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="btn-group">
                @foreach(\App\Http\Controllers\Crm\SalesReportController::PERIODS as $option)
                    <a href="{{ route('reports.sales', ['period' => $option]) }}"
                       class="btn {{ $period === $option ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ ucfirst($option) }} Sales
                    </a>
                @endforeach
            </div>
            <span class="text-muted">{{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"><x-kpi-card label="Revenue" :value="'₱'.number_format($summary['revenue'], 2)" icon="bi-cash-stack" /></div>
        <div class="col-md-3"><x-kpi-card label="Completed Orders" :value="$summary['completed_orders']" icon="bi-bag-check"
                                          :href="route('sales.index', ['status' => 'completed'])" hint="View entries" /></div>
        <div class="col-md-3"><x-kpi-card label="Transactions" :value="$summary['transactions']" icon="bi-receipt"
                                          :href="route('sales.index')" hint="All POS entries" /></div>
        <div class="col-md-3"><x-kpi-card label="Average Order" :value="'₱'.number_format($summary['average_order'], 2)" icon="bi-calculator" /></div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">{{ ucfirst($period) }} Revenue</h5></div>
                <div class="card-body"><canvas id="salesChart" height="110"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Revenue by Category</h5></div>
                <div class="card-body"><canvas id="categoryChart" height="180"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white"><h5 class="mb-0">{{ ucfirst($period) }} Breakdown</h5></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Period</th><th>Completed Orders</th><th>Revenue</th></tr></thead>
                <tbody>
                @forelse($breakdown as $label => $row)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $row['orders'] }}</td>
                        <td>₱{{ number_format($row['revenue'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No completed sales in this period.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td>Total</td>
                        <td>{{ $summary['completed_orders'] }}</td>
                        <td>₱{{ number_format($summary['revenue'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0">Sales Transactions</h5>
            <span class="text-muted">Refunded: ₱{{ number_format($summary['refunded'], 2) }}</span>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Reference</th><th>Date</th><th>Guest</th><th>Branch</th><th>Description</th><th>Recorded By</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->reference }}</td>
                        <td>{{ $sale->sold_at->format('M d, Y H:i') }}</td>
                        <td>{{ $sale->guest?->full_name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->branch?->name ?? '—' }}</td>
                        <td>{{ $sale->description }}</td>
                        <td>{{ $sale->recorder?->name ?? '—' }}</td>
                        <td>₱{{ number_format($sale->amount, 2) }}</td>
                        <td><span class="badge {{ $sale->status === 'completed' ? 'badge-success' : ($sale->status === 'refunded' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($sale->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No transactions in this period.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('salesChart'), {
        type: '{{ $period === 'daily' ? 'line' : 'bar' }}',
        data: {
            labels: @json($chart['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($chart['values']),
                borderColor: '#1f3b57',
                backgroundColor: 'rgba(31, 59, 87, 0.15)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byCategory->keys()),
            datasets: [{
                data: @json($byCategory->values()),
                backgroundColor: ['#1f3b57', '#2f7fa8', '#59b3a9', '#f0a202', '#d64545', '#8e6cb0'],
            }]
        },
        options: { responsive: true }
    });
</script>
@endpush
