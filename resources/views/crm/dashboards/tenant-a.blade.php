@extends('layouts.crm')

@section('title', 'Front Desk')
@section('page-title', 'Front Desk Operations')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <x-kpi-card label="Total Guests" :value="number_format($summary['total_guests'])"
                        icon="bi-people" :href="route('guests.index')" hint="Open guest registry" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Total Reservations" :value="number_format($summary['total_reservations'])"
                        icon="bi-calendar-check" :href="route('reservations.index')" hint="Open reservations" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Revenue This Month" :value="'₱'.number_format($summary['revenue_this_month'], 2)"
                        icon="bi-cash-coin" :href="route('reports.sales')" hint="Open sales report" />
        </div>
        <div class="col-md-3">
            <x-kpi-card label="Unresolved Feedback" :value="number_format($summary['unresolved_feedback'])"
                        icon="bi-chat-left-dots" :href="route('feedback.index', ['status' => 'unresolved'])"
                        hint="Review unresolved feedback" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap gap-2">
            <a href="{{ route('reservations.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Reservation</a>
            <a href="{{ route('guests.create') }}" class="btn btn-outline-primary"><i class="bi bi-person-plus"></i> Register Guest</a>
            <a href="{{ route('sales.create') }}" class="btn btn-outline-primary"><i class="bi bi-receipt"></i> New POS Entry</a>
            <a href="{{ route('interactions.create') }}" class="btn btn-outline-primary"><i class="bi bi-journal-plus"></i> Log Interaction</a>
            <a href="{{ route('feedback.create') }}" class="btn btn-outline-primary"><i class="bi bi-chat-square-text"></i> Record Feedback</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Arrivals Today</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Guest</th><th>Room</th><th>Nights</th><th></th></tr></thead>
                        <tbody>
                        @forelse($todayArrivals as $reservation)
                            <tr>
                                <td>{{ $reservation->guest->full_name }}</td>
                                <td>{{ $reservation->room_number }}</td>
                                <td>{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }}</td>
                                <td>
                                    <form action="{{ route('reservations.check-in', $reservation) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Check In</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No arrivals scheduled for today.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">In-House Guests</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Guest</th><th>Room</th><th>Departure</th><th></th></tr></thead>
                        <tbody>
                        @forelse($inHouse as $reservation)
                            <tr>
                                <td>{{ $reservation->guest->full_name }}</td>
                                <td>{{ $reservation->room_number }}</td>
                                <td>{{ $reservation->check_out_date->format('M d, Y') }}</td>
                                <td>
                                    <form action="{{ route('reservations.check-out', $reservation) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary">Check Out</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No guests are currently checked in.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Recent Point-of-Sale Entries</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Reference</th><th>Description</th><th>Guest</th><th>Amount</th></tr></thead>
                        <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->reference }}</td>
                                <td>{{ $sale->description }}</td>
                                <td>{{ $sale->guest?->full_name ?? 'Walk-in' }}</td>
                                <td>₱{{ number_format($sale->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No sales recorded yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Latest Interactions</h5></div>
                <div class="card-body">
                    @forelse($recentInteractions as $interaction)
                        <div class="board-card">
                            <span class="badge badge-info">{{ $interaction->type }}</span>
                            <strong class="d-block mt-2">{{ $interaction->subject }}</strong>
                            <small class="text-muted">{{ $interaction->guest->full_name }} · {{ $interaction->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No interactions logged yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
