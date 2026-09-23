@extends('layouts.crm')

@section('title', $guest->full_name)
@section('page-title', 'Guest Profile')

@section('content')
    <div class="card">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
            <div>
                <h4>{{ $guest->full_name }}</h4>
                <p class="mb-1 text-muted">{{ $guest->email ?? '—' }} · {{ $guest->phone ?? '—' }}</p>
                <p class="mb-1 text-muted">{{ $guest->address }}</p>
                <span class="badge {{ $guest->status === 'active' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($guest->status) }}</span>
                @if($guest->branch)<span class="badge badge-info">{{ $guest->branch->name }}</span>@endif
            </div>
            <div class="d-flex gap-2 align-items-start">
                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-outline-primary">Edit</a>
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">New Reservation</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Reservations</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Reference</th><th>Room</th><th>Dates</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($guest->reservations as $reservation)
                            <tr>
                                <td><a href="{{ route('reservations.show', $reservation) }}">{{ $reservation->reference }}</a></td>
                                <td>{{ $reservation->room_number }}</td>
                                <td>{{ $reservation->check_in_date->format('M d') }} – {{ $reservation->check_out_date->format('M d, Y') }}</td>
                                <td><span class="badge {{ $reservation->statusBadge() }}">{{ str_replace('_', ' ', $reservation->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No reservations.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Purchases</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                        <tbody>
                        @forelse($guest->sales as $sale)
                            <tr>
                                <td>{{ $sale->sold_at->format('M d, Y') }}</td>
                                <td>{{ $sale->description }}</td>
                                <td>₱{{ number_format($sale->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No purchases.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Feedback</h5></div>
                <div class="card-body">
                    @forelse($guest->feedback as $item)
                        <div class="board-card">
                            <div class="d-flex justify-content-between">
                                <span>{{ str_repeat('★', $item->rating) }}</span>
                                <span class="badge {{ $item->status === 'resolved' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($item->status) }}</span>
                            </div>
                            <small class="text-muted">{{ $item->comment }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No feedback submitted.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Interaction Log</h5></div>
                <div class="card-body">
                    @forelse($guest->interactions as $interaction)
                        <div class="board-card">
                            <span class="badge badge-info">{{ $interaction->type }}</span>
                            <strong class="d-block mt-1">{{ $interaction->subject }}</strong>
                            <small class="text-muted">{{ $interaction->details }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No interactions logged.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
