@extends('layouts.crm')

@section('title', 'Platform Overview')
@section('page-title', 'Master Business Intelligence')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Active Tenants" :value="$stats['active_tenants'].' / '.$stats['tenants']"
                        icon="bi-diagram-3" :href="route('master.tenants.index')" hint="Manage tenants" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Total System Revenue" :value="'₱'.number_format($stats['revenue'], 2)"
                        icon="bi-cash-stack" :href="route('reports.sales')" hint="Open sales report" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Reservations (All Properties)" :value="number_format($stats['reservations'])"
                        icon="bi-calendar-check" :href="route('reservations.index')" hint="View reservations" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Monthly Recurring Revenue" :value="'₱'.number_format($stats['mrr'], 2)"
                        icon="bi-credit-card" :href="route('master.subscriptions.index')" hint="View subscriptions" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Guests Across Platform" :value="number_format($stats['guests'])"
                        icon="bi-people" :href="route('guests.index')" hint="View guests" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Property Branches" :value="number_format($stats['branches'])" icon="bi-building" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Suspended Tenants" :value="number_format($stats['suspended_tenants'])"
                        icon="bi-slash-circle" :href="route('master.tenants.index')" hint="Review status" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Platform Perspective" value="Master" icon="bi-globe2" />
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Tenant Performance</h5>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Code</th>
                        <th>Plan</th>
                        <th>Guests</th>
                        <th>Reservations</th>
                        <th>Revenue</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perTenant as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->code }}</td>
                            <td>{{ $row->subscription?->plan ?? '—' }}</td>
                            <td>{{ $row->guests_count }}</td>
                            <td>{{ $row->reservations_count }}</td>
                            <td>₱{{ number_format($row->revenue, 2) }}</td>
                            <td>
                                <span class="badge {{ $row->isActive() ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('context.tenant') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tenant_id" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-primary">Switch to tenant</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
