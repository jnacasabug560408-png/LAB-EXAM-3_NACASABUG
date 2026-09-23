@extends('layouts.crm')

@section('title', $reservation->reference)
@section('page-title', 'Reservation '.$reservation->reference)

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-3">
                <div>
                    <h4>{{ $reservation->guest->full_name }}</h4>
                    <p class="mb-1 text-muted">
                        Room {{ $reservation->room_number }} ({{ ucfirst($reservation->room_type) }}) ·
                        {{ $reservation->check_in_date->format('M d, Y') }} – {{ $reservation->check_out_date->format('M d, Y') }}
                    </p>
                    <p class="mb-1 text-muted">{{ $reservation->adults }} adult(s), {{ $reservation->children }} child(ren)</p>
                    <span class="badge {{ $reservation->statusBadge() }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    @if($reservation->branch)<span class="badge badge-info">{{ $reservation->branch->name }}</span>@endif
                </div>
                <div class="text-end">
                    <h3>₱{{ number_format($reservation->total_amount, 2) }}</h3>
                    <div class="d-flex gap-2 mt-2">
                        @if($reservation->status === 'checked_in')
                            <form action="{{ route('reservations.check-out', $reservation) }}" method="POST">
                                @csrf<button class="btn btn-primary">Check Out</button>
                            </form>
                        @elseif(! in_array($reservation->status, ['checked_out', 'cancelled']))
                            <form action="{{ route('reservations.check-in', $reservation) }}" method="POST">
                                @csrf<button class="btn btn-success">Check In</button>
                            </form>
                        @endif
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-outline-primary">Edit</a>
                    </div>
                </div>
            </div>
            @if($reservation->notes)
                <hr><p class="mb-0"><strong>Notes:</strong> {{ $reservation->notes }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white"><h5 class="mb-0">Charges</h5></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Reference</th><th>Description</th><th>Category</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($reservation->sales as $sale)
                    <tr>
                        <td>{{ $sale->reference }}</td>
                        <td>{{ $sale->description }}</td>
                        <td>{{ ucfirst($sale->category) }}</td>
                        <td>₱{{ number_format($sale->amount, 2) }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($sale->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No charges posted.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Back to reservations</a>
@endsection
