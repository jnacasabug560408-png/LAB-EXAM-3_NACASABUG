@extends('layouts.crm')

@section('title', 'Branches')
@section('page-title', 'Branch Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Manage property branches. Use the branch filter in the header to scope all data and analytics.</p>
        <a href="{{ route('branches.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Branch</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Branch</th><th>Code</th><th>Address</th><th>Manager</th><th>Rooms</th><th>Guests</th><th>Reservations</th><th>Revenue</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->code }}</td>
                        <td>{{ $branch->address ?? '—' }}</td>
                        <td>{{ $branch->manager_name ?? '—' }}</td>
                        <td>{{ $branch->total_rooms }}</td>
                        <td>{{ $branch->guests_total }}</td>
                        <td>{{ $branch->reservations_total }}</td>
                        <td>₱{{ number_format($branch->revenue_total, 2) }}</td>
                        <td><span class="badge {{ $branch->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($branch->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <form action="{{ route('context.branch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                    <button class="btn btn-sm btn-outline-primary">Filter</button>
                                </form>
                                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Delete this branch?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">No branches yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
