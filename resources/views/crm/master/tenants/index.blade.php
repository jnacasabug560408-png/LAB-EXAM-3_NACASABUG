@extends('layouts.crm')

@section('title', 'Tenant Administration')
@section('page-title', 'Tenant Administration')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Create, manage, activate or suspend tenants on the InnEase platform.</p>
        <a href="{{ route('master.tenants.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Tenant</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th><th>Code</th><th>Tier</th><th>Branching</th>
                        <th>Users</th><th>Guests</th><th>Reservations</th><th>Plan</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td><a href="{{ route('master.tenants.show', $tenant) }}">{{ $tenant->name }}</a></td>
                        <td>{{ $tenant->code }}</td>
                        <td>{{ ucfirst($tenant->tier) }}</td>
                        <td>{{ $tenant->supports_branching ? 'Multi-branch' : 'Single property' }}</td>
                        <td>{{ $tenant->users_count }}</td>
                        <td>{{ $tenant->guests_count }}</td>
                        <td>{{ $tenant->reservations_count }}</td>
                        <td>{{ $tenant->subscription?->plan ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $tenant->isActive() ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('master.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('master.tenants.toggle-status', $tenant) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm {{ $tenant->isActive() ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $tenant->isActive() ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                <form action="{{ route('master.tenants.destroy', $tenant) }}" method="POST"
                                      onsubmit="return confirm('Delete this tenant and all of its data?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No tenants yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
