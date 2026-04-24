@extends('layouts.app')

@section('title', 'Member Details')
@section('page-title', 'Member Profile')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Member Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Member ID:</strong> {{ $member->member_id }}</p>
                    <p><strong>Name:</strong> {{ $member->name }}</p>
                    <p><strong>Email:</strong> {{ $member->email }}</p>
                    <p><strong>Phone:</strong> {{ $member->phone }}</p>
                    <p><strong>Address:</strong> {{ $member->address }}</p>
                    <p><strong>Member Since:</strong> {{ $member->membership_date->format('M d, Y') }}</p>
                    <p>
                        <strong>Status:</strong>
                        @if($member->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($member->status == 'inactive')
                            <span class="badge badge-info">Inactive</span>
                        @else
                            <span class="badge badge-danger">Suspended</span>
                        @endif
                    </p>

                    <hr>

                    <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm me-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Borrowing History</h5>
                    <span class="badge badge-light">{{ $borrowings->total() }} Records</span>
                </div>
                <div class="card-body p-0">
                    @if($borrowings->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Book</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($borrowings as $borrowing)
                                    <tr>
                                        <td>{{ $borrowing->book->title }}</td>
                                        <td>{{ $borrowing->borrow_date->format('M d, Y') }}</td>
                                        <td>{{ $borrowing->due_date->format('M d, Y') }}</td>
                                        <td>{{ $borrowing->return_date ? $borrowing->return_date->format('M d, Y') : '-' }}</td>
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
                        <p class="text-muted p-3 mb-0">No borrowing records</p>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($borrowings->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $borrowings->links() }}
                </div>
            @endif

            <div class="card mt-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Penalties</h5>
                    <span class="badge badge-dark">{{ $penalties->total() }} Records</span>
                </div>
                <div class="card-body p-0">
                    @if($penalties->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Amount</th>
                                    <th>Days Overdue</th>
                                    <th>Status</th>
                                    <th>Penalty Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penalties as $penalty)
                                    <tr>
                                        <td>₱{{ number_format($penalty->penalty_amount, 2) }}</td>
                                        <td>{{ $penalty->days_overdue }} days</td>
                                        <td>
                                            @if($penalty->payment_status == 'unpaid')
                                                <span class="badge badge-danger">Unpaid</span>
                                            @elseif($penalty->payment_status == 'paid')
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-secondary">Waived</span>
                                            @endif
                                        </td>
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
                        <p class="text-muted p-3 mb-0">No penalties</p>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($penalties->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $penalties->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection