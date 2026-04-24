@extends('layouts.app')

@section('title', 'Borrowing Details')
@section('page-title', 'Borrowing Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Transaction Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Member Information</h6>
                            <p>
                                <strong>Name:</strong> 
                                <a href="{{ route('members.show', $borrowing->member) }}">
                                    {{ $borrowing->member->name }}
                                </a>
                            </p>
                            <p><strong>Member ID:</strong> {{ $borrowing->member->member_id }}</p>
                            <p><strong>Email:</strong> {{ $borrowing->member->email }}</p>
                            <p><strong>Phone:</strong> {{ $borrowing->member->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Book Information</h6>
                            <p>
                                <strong>Title:</strong> 
                                <a href="{{ route('books.show', $borrowing->book) }}">
                                    {{ $borrowing->book->title }}
                                </a>
                            </p>
                            <p><strong>Author:</strong> {{ $borrowing->book->author }}</p>
                            <p><strong>ISBN:</strong> {{ $borrowing->book->isbn }}</p>
                            <p><strong>Quantity:</strong> {{ $borrowing->quantity }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p><strong>Borrow Date:</strong></p>
                            <p class="text-lg">{{ $borrowing->borrow_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Due Date:</strong></p>
                            <p class="text-lg">{{ $borrowing->due_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Return Date:</strong></p>
                            <p class="text-lg">{{ $borrowing->return_date ? $borrowing->return_date->format('M d, Y') : '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong></p>
                            @if($borrowing->status == 'borrowed')
                                <span class="badge badge-info" style="font-size: 16px; padding: 8px 16px;">Borrowed</span>
                            @elseif($borrowing->status == 'returned')
                                <span class="badge badge-success" style="font-size: 16px; padding: 8px 16px;">Returned</span>
                            @else
                                <span class="badge badge-danger" style="font-size: 16px; padding: 8px 16px;">Overdue</span>
                            @endif
                        </div>
                        @if($borrowing->status !== 'returned')
                            <div class="col-md-6">
                                <p><strong>Days Since Due:</strong></p>
                                <p class="text-lg">{{ $borrowing->getDaysOverdue() }} days</p>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div>
                        @if($borrowing->status !== 'returned')
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#returnModal">
                                <i class="bi bi-check-circle"></i> Return Book
                            </button>
                        @endif
                        <a href="{{ route('borrowings.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($borrowing->penalty)
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Associated Penalty</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Amount:</strong> ₱{{ number_format($borrowing->penalty->penalty_amount, 2) }}</p>
                        <p><strong>Days Overdue:</strong> {{ $borrowing->penalty->days_overdue }}</p>
                        <p>
                            <strong>Status:</strong><br>
                            @if($borrowing->penalty->payment_status == 'unpaid')
                                <span class="badge badge-danger">Unpaid</span>
                            @elseif($borrowing->penalty->payment_status == 'paid')
                                <span class="badge badge-success">Paid</span>
                            @else
                                <span class="badge badge-secondary">Waived</span>
                            @endif
                        </p>
                        <a href="{{ route('penalties.show', $borrowing->penalty) }}" class="btn btn-sm btn-primary w-100">
                            View Penalty Details
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('borrowings.return', $borrowing) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Return Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Book:</strong> {{ $borrowing->book->title }}</p>
                        <p><strong>Member:</strong> {{ $borrowing->member->name }}</p>
                        <p><strong>Due Date:</strong> {{ $borrowing->due_date->format('M d, Y') }}</p>
                        
                        <div class="form-group">
                            <label for="return_date" class="form-label">Return Date</label>
                            <input type="date" class="form-control" id="return_date" 
                                   name="return_date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Return Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection