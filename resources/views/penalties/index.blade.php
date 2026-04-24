@extends('layouts.app')

@section('title', 'Penalties')
@section('page-title', 'Penalty Management')

@section('content')
    <h2 class="mb-4">Penalties & Overdue Fines</h2>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Penalties</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['unpaid'] }}</h3>
                <p>Unpaid Penalties</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>₱{{ number_format($stats['total_amount'], 2) }}</h3>
                <p>Total Unpaid Amount</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['paid'] }}</h3>
                <p>Paid Penalties</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($penalties->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Days Overdue</th>
                            <th>Penalty Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penalties as $penalty)
                            <tr>
                                <td>
                                    <a href="{{ route('members.show', $penalty->member) }}">
                                        {{ $penalty->member->name }}
                                    </a>
                                </td>
                                <td><strong>₱{{ number_format($penalty->penalty_amount, 2) }}</strong></td>
                                <td>{{ $penalty->days_overdue }} days</td>
                                <td>{{ $penalty->penalty_date->format('M d, Y') }}</td>
                                <td>
                                    @if($penalty->payment_status == 'unpaid')
                                        <span class="badge badge-danger">Unpaid</span>
                                    @elseif($penalty->payment_status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-secondary">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('penalties.show', $penalty) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($penalty->payment_status === 'unpaid')
                                            <form action="{{ route('penalties.mark-paid', $penalty) }}" method="POST" 
                                                  style="display: inline;" title="Mark as Paid">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i> No penalties found.
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $penalties->links() }}
    </div>
@endsection