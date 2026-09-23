@extends('layouts.crm')

@section('title', 'Guests')
@section('page-title', 'Guest Registry')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, email or phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('guests.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('guests.create') }}" class="btn btn-success ms-auto"><i class="bi bi-person-plus"></i> Register Guest</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Contact</th><th>Branch</th><th>Reservations</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td><a href="{{ route('guests.show', $guest) }}">{{ $guest->full_name }}</a></td>
                        <td>{{ $guest->email ?? '—' }}<br><small class="text-muted">{{ $guest->phone }}</small></td>
                        <td>{{ $guest->branch?->name ?? '—' }}</td>
                        <td>{{ $guest->reservations_count }}</td>
                        <td><span class="badge {{ $guest->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($guest->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" onsubmit="return confirm('Delete this guest?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No guests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $guests->links() }}
@endsection
