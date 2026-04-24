@extends('layouts.app')

@section('title', 'Borrowings')
@section('page-title', 'Borrowing Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Borrowing Transactions</h2>
        <div>
            <a href="{{ route('borrowings.create') }}" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle"></i> New Borrowing
            </a>
            <a href="{{ route('borrowings.report') }}" class="btn btn-info">
                <i class="bi bi-file-earmark-pdf"></i> Report
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Transactions</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['borrowed'] }}</h3>
                <p>Currently Borrowed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['returned'] }}</h3>
                <p>Returned</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3 style="color: #e74c3c;">{{ $stats['overdue'] }}</h3>
                <p>Overdue Items</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($borrowings->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Book</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('members.show', $borrowing->member) }}">
                                        {{ $borrowing->member->name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('books.show', $borrowing->book) }}">
                                        {{ $borrowing->book->title }}
                                    </a>
                                </td>
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
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('borrowings.show', $borrowing) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($borrowing->status !== 'returned')
                                            <a href="{{ route('borrowings.edit', $borrowing) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if($borrowing->status === 'borrowed' || $borrowing->status === 'overdue')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" 
                                                    data-bs-target="#returnModal{{ $borrowing->id }}" title="Return">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i> No borrowing records found. <a href="{{ route('borrowings.create') }}">Create one now</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $borrowings->links() }}
    </div>

    <!-- Return Modals -->
    @foreach($borrowings as $borrowing)
        <div class="modal fade" id="returnModal{{ $borrowing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('borrowings.return', $borrowing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Return Book</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Member:</strong> {{ $borrowing->member->name }}</p>
                            <p><strong>Book:</strong> {{ $borrowing->book->title }}</p>
                            <p><strong>Due Date:</strong> {{ $borrowing->due_date->format('M d, Y') }}</p>
                            
                            <div class="form-group">
                                <label for="return_date{{ $borrowing->id }}" class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="return_date{{ $borrowing->id }}" 
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
    @endforeach
@endsection