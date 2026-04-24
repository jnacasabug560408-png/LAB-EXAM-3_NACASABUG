@extends('layouts.app')

@section('title', 'Penalty Details')
@section('page-title', 'Penalty Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Penalty Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Member Details</h6>
                            <p>
                                <strong>Name:</strong> 
                                <a href="{{ route('members.show', $penalty->member) }}">
                                    {{ $penalty->member->name }}
                                </a>
                            </p>
                            <p><strong>Member ID:</strong> {{ $penalty->member->member_id }}</p>
                            <p><strong>Email:</strong> {{ $penalty->member->email }}</p>
                            <p><strong>Phone:</strong> {{ $penalty->member->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Borrowing Details</h6>
                            <p>
                                <strong>Book:</strong> 
                                <a href="{{ route('books.show', $penalty->borrowing->book) }}">
                                    {{ $penalty->borrowing->book->title }}
                                </a>
                            </p>
                            <p><strong>Borrow Date:</strong> {{ $penalty->borrowing->borrow_date->format('M d, Y') }}</p>
                            <p><strong>Due Date:</strong> {{ $penalty->borrowing->due_date->format('M d, Y') }}</p>
                            <p><strong>Return Date:</strong> {{ $penalty->borrowing->return_date ? $penalty->borrowing->return_date->format('M d, Y') : '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p><strong>Days Overdue:</strong></p>
                            <p class="text-lg">{{ $penalty->days_overdue }} days</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Penalty Amount:</strong></p>
                            <p class="text-lg" style="font-size: 24px; color: var(--danger-color); font-weight: bold;">
                                ₱{{ number_format($penalty->penalty_amount, 2) }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Penalty Date:</strong></p>
                            <p class="text-lg">{{ $penalty->penalty_date->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Payment Status:</strong></p>
                            @if($penalty->payment_status == 'unpaid')
                                <span class="badge badge-danger" style="font-size: 14px; padding: 8px 12px;">Unpaid</span>
                            @elseif($penalty->payment_status == 'paid')
                                <span class="badge badge-success" style="font-size: 14px; padding: 8px 12px;">Paid</span>
                                <p class="mt-2"><strong>Paid Date:</strong> {{ $penalty->paid_date->format('M d, Y') }}</p>
                            @else
                                <span class="badge badge-secondary" style="font-size: 14px; padding: 8px 12px;">Waived</span>
                            @endif
                        </div>
                    </div>

                    @if($penalty->remarks)
                        <div class="alert alert-info">
                            <strong>Remarks:</strong> {{ $penalty->remarks }}
                        </div>
                    @endif

                    <hr>

                    <div>
                        @if($penalty->payment_status === 'unpaid')
                            <form action="{{ route('penalties.mark-paid', $penalty) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="bi bi-check-circle"></i> Mark as Paid
                                </button>
                            </form>

                            <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#waiveModal">
                                <i class="bi bi-x-circle"></i> Waive Penalty
                            </button>
                        @endif

                        <a href="{{ route('penalties.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Unpaid by Member:</strong></p>
                    <p style="font-size: 20px; color: var(--danger-color);">
                        ₱{{ number_format($penalty->member->totalPenalties(), 2) }}
                    </p>

                    <hr>

                    <p><strong>Member Status:</strong></p>
                    @if($penalty->member->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($penalty->member->status == 'inactive')
                        <span class="badge badge-info">Inactive</span>
                    @else
                        <span class="badge badge-danger">Suspended</span>
                    @endif

                    <hr>

                    <p><strong>Can Borrow:</strong></p>
                    @if($penalty->member->canBorrow())
                        <span class="badge badge-success">Yes</span>
                    @else
                        <span class="badge badge-danger">No</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Waive Modal -->
    <div class="modal fade" id="waiveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('penalties.waive', $penalty) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Waive Penalty</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> You are about to waive a penalty of <strong>₱{{ number_format($penalty->penalty_amount, 2) }}</strong>
                        </div>

                        <div class="form-group">
                            <label for="remarks" class="form-label">Reason for Waiving</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="4" 
                                      placeholder="Enter reason for waiving this penalty (optional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Waive Penalty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection