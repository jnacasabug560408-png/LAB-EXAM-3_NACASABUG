@extends('layouts.crm')

@section('title', 'Reservations')
@section('page-title', 'Reservations')

@section('content')
    <div class="row">
        @foreach($statusCounts as $status => $count)
            <div class="col-md-2">
                <x-kpi-card :label="ucfirst(str_replace('_', ' ', $status))" :value="$count" icon="bi-calendar-check"
                            :href="route('reservations.index', ['status' => $status])" hint="Filter" />
            </div>
        @endforeach
        <div class="col-md-2">
            <x-kpi-card label="All" :value="array_sum($statusCounts)" icon="bi-list-ul"
                        :href="route('reservations.index')" hint="Clear filter" />
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Reference, room or guest">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(\App\Models\Reservation::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('reservations.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-circle"></i> New Reservation</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Reference</th><th>Guest</th><th>Room</th><th>Dates</th><th>Branch</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($reservations as $reservation)
                    <tr>
                        <td><a href="{{ route('reservations.show', $reservation) }}">{{ $reservation->reference }}</a></td>
                        <td>{{ $reservation->guest->full_name }}</td>
                        <td>{{ $reservation->room_number }} <small class="text-muted">({{ $reservation->room_type }})</small></td>
                        <td>{{ $reservation->check_in_date->format('M d') }} – {{ $reservation->check_out_date->format('M d, Y') }}</td>
                        <td>{{ $reservation->branch?->name ?? '—' }}</td>
                        <td>₱{{ number_format($reservation->total_amount, 2) }}</td>
                        <td><span class="badge {{ $reservation->statusBadge() }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($reservation->status === 'checked_in')
                                    <form action="{{ route('reservations.check-out', $reservation) }}" method="POST">
                                        @csrf<button class="btn btn-sm btn-outline-primary">Check Out</button>
                                    </form>
                                @elseif(! in_array($reservation->status, ['checked_out', 'cancelled']))
                                    <form action="{{ route('reservations.check-in', $reservation) }}" method="POST">
                                        @csrf<button class="btn btn-sm btn-success">Check In</button>
                                    </form>
                                @endif
                                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No reservations found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $reservations->links() }}
@endsection
