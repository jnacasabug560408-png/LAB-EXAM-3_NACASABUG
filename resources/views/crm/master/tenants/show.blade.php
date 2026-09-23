@extends('layouts.crm')

@section('title', $tenant->name)
@section('page-title', $tenant->name)

@section('content')
    <div class="row">
        <div class="col-md-3"><x-kpi-card label="Users" :value="$tenant->users_count" icon="bi-person-badge" /></div>
        <div class="col-md-3"><x-kpi-card label="Branches" :value="$tenant->branches_count" icon="bi-building" /></div>
        <div class="col-md-3"><x-kpi-card label="Guests" :value="$tenant->guests_count" icon="bi-people" /></div>
        <div class="col-md-3"><x-kpi-card label="Reservations" :value="$tenant->reservations_count" icon="bi-calendar-check" /></div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Profile</h5></div>
                <div class="card-body">
                    <p><strong>Code:</strong> {{ $tenant->code }}</p>
                    <p><strong>Tier:</strong> {{ ucfirst($tenant->tier) }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $tenant->isActive() ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($tenant->status) }}</span>
                    </p>
                    <p><strong>Branching:</strong> {{ $tenant->supports_branching ? 'Enabled' : 'Disabled' }}</p>
                    <p><strong>Contact:</strong> {{ $tenant->contact_email ?? '—' }} · {{ $tenant->contact_phone ?? '—' }}</p>
                    <p class="mb-0">{{ $tenant->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Subscriptions</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Plan</th><th>Price</th><th>Renews</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($tenant->subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->plan }}</td>
                                <td>₱{{ number_format($subscription->monthly_price, 2) }}</td>
                                <td>{{ $subscription->renews_on->format('M d, Y') }}</td>
                                <td><span class="badge badge-info">{{ ucfirst($subscription->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No subscription on file.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Branches</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Name</th><th>Code</th><th>Rooms</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($tenant->branches as $branch)
                            <tr>
                                <td>{{ $branch->name }}</td><td>{{ $branch->code }}</td>
                                <td>{{ $branch->total_rooms }}</td><td>{{ ucfirst($branch->status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No branches.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Users</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                        <tbody>
                        @forelse($tenant->users as $user)
                            <tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ ucfirst($user->role) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No users.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('master.tenants.index') }}" class="btn btn-outline-secondary">Back to tenants</a>
@endsection
