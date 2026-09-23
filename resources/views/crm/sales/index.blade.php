@extends('layouts.crm')

@section('title', 'Point of Sale')
@section('page-title', 'Point-of-Sale Entries')

@section('content')
    <div class="row">
        <div class="col-md-4"><x-kpi-card label="Sales Today" :value="'₱'.number_format($totals['today'], 2)" icon="bi-cash" /></div>
        <div class="col-md-4"><x-kpi-card label="Sales This Month" :value="'₱'.number_format($totals['month'], 2)" icon="bi-graph-up"
                                          :href="route('reports.sales', ['period' => 'monthly'])" hint="Open sales report" /></div>
        <div class="col-md-4"><x-kpi-card label="Transactions" :value="$totals['transactions']" icon="bi-receipt" /></div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        @foreach(['room', 'food', 'beverage', 'spa', 'laundry', 'other'] as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['completed', 'pending', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('sales.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-circle"></i> New POS Entry</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Reference</th><th>Date</th><th>Guest</th><th>Description</th><th>Qty</th><th>Amount</th><th>Payment</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->reference }}</td>
                        <td>{{ $sale->sold_at->format('M d, Y H:i') }}</td>
                        <td>{{ $sale->guest?->full_name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->description }}<br><small class="text-muted">{{ ucfirst($sale->category) }}</small></td>
                        <td>{{ $sale->quantity }}</td>
                        <td>₱{{ number_format($sale->amount, 2) }}</td>
                        <td>{{ ucfirst($sale->payment_method) }}</td>
                        <td><span class="badge {{ $sale->status === 'completed' ? 'badge-success' : ($sale->status === 'refunded' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($sale->status) }}</span></td>
                        <td>
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Remove this entry?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No sales recorded.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $sales->links() }}
@endsection
