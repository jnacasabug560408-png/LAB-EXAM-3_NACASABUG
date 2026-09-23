@extends('layouts.crm')

@section('title', 'Subscriptions')
@section('page-title', 'Subscription Management')

@section('content')
    <div class="row">
        <div class="col-md-4"><x-kpi-card label="Active Plans" :value="$stats['active']" icon="bi-patch-check" /></div>
        <div class="col-md-4"><x-kpi-card label="Monthly Recurring Revenue" :value="'₱'.number_format($stats['mrr'], 2)" icon="bi-cash-stack" /></div>
        <div class="col-md-4"><x-kpi-card label="Renewing Within 30 Days" :value="$stats['renewing_soon']" icon="bi-calendar-event" /></div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Track plans, renewal dates and feature access per tenant.</p>
        <a href="{{ route('master.subscriptions.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Plan</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr><th>Tenant</th><th>Plan</th><th>Monthly Price</th><th>Started</th><th>Renews</th><th>Features</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                @forelse($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->tenant->name }} ({{ $subscription->tenant->code }})</td>
                        <td>{{ $subscription->plan }}</td>
                        <td>₱{{ number_format($subscription->monthly_price, 2) }}</td>
                        <td>{{ $subscription->started_on->format('M d, Y') }}</td>
                        <td>{{ $subscription->renews_on->format('M d, Y') }}</td>
                        <td>
                            @foreach($subscription->featureList() as $feature)
                                <span class="badge badge-info mb-1">{{ $feature }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('master.subscriptions.edit', $subscription) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('master.subscriptions.destroy', $subscription) }}" method="POST"
                                      onsubmit="return confirm('Remove this subscription?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No subscriptions recorded.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
