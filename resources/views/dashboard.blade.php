@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <i class="bi bi-book" style="font-size: 32px; color: var(--secondary-color);"></i>
                <h3>{{ $stats['books'] }}</h3>
                <p>Total Books</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <i class="bi bi-people" style="font-size: 32px; color: var(--success-color);"></i>
                <h3>{{ $stats['members'] }}</h3>
                <p>Active Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <i class="bi bi-arrow-left-right" style="font-size: 32px; color: var(--warning-color);"></i>
                <h3>{{ $stats['borrowed'] }}</h3>
                <p>Active Borrowings</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <i class="bi bi-exclamation-triangle" style="font-size: 32px; color: var(--danger-color);"></i>
                <h3>{{ $stats['overdue'] }}</h3>
                <p>Overdue Items</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recent Borrowings</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentBorrowings->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Book</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBorrowings as $borrowing)
                                    <tr>
                                        <td>{{ $borrowing->member->name }}</td>
                                        <td>{{ $borrowing->book->title }}</td>
                                        <td>
                                            @if($borrowing->status == 'borrowed')
                                                <span class="badge badge-info">Borrowed</span>
                                            @elseif($borrowing->status == 'returned')
                                                <span class="badge badge-success">Returned</span>
                                            @else
                                                <span class="badge badge-danger">Overdue</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted p-3 mb-0">No recent borrowings</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Overdue Borrowings</h5>
                </div>
                <div class="card-body p-0">
                    @if($overdueBorrowings->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Book</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueBorrowings as $borrowing)
                                    <tr>
                                        <td>{{ $borrowing->member->name }}</td>
                                        <td>{{ $borrowing->book->title }}</td>
                                        <td>{{ $borrowing->due_date->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted p-3 mb-0">No overdue items</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Unpaid Penalties</h5>
                </div>
                <div class="card-body p-0">
                    @if($unpaidPenalties->count() > 0)
                        <div class="alert alert-warning mb-3 mt-3 ms-3 me-3">
                            <strong>Total Unpaid:</strong> ₱{{ number_format($stats['unpaid_penalties'], 2) }}
                            <strong class="ms-4">Count:</strong> {{ $stats['total_penalties_count'] }}
                        </div>
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Penalty Amount</th>
                                    <th>Days Overdue</th>
                                    <th>Penalty Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unpaidPenalties as $penalty)
                                    <tr>
                                        <td>{{ $penalty->member->name }}</td>
                                        <td>₱{{ number_format($penalty->penalty_amount, 2) }}</td>
                                        <td>{{ $penalty->days_overdue }} days</td>
                                        <td>{{ $penalty->penalty_date->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('penalties.show', $penalty) }}" class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted p-3 mb-0">No unpaid penalties</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection