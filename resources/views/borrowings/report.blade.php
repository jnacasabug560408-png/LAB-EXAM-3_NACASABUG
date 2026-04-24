@extends('layouts.app')

@section('title', 'Borrowings Report')
@section('page-title', 'Borrowing Report')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Borrowing Report</h2>
        <button onclick="window.print()" class="btn btn-info">
            <i class="bi bi-printer"></i> Print/Export PDF
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="reportTable">
                <thead class="table-light">
                    <tr>
                        <th>Member ID</th>
                        <th>Member Name</th>
                        <th>Book ISBN</th>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrowings as $borrowing)
                        <tr>
                            <td>{{ $borrowing->member->member_id }}</td>
                            <td>{{ $borrowing->member->name }}</td>
                            <td>{{ $borrowing->book->isbn }}</td>
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
                            <td>
                                @if($borrowing->status === 'returned')
                                    {{ $borrowing->return_date > $borrowing->due_date ? $borrowing->return_date->diffInDays($borrowing->due_date) : 0 }}
                                @else
                                    {{ \Carbon\Carbon::now() > $borrowing->due_date ? \Carbon\Carbon::now()->diffInDays($borrowing->due_date) : 0 }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('borrowings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Borrowings
        </a>
    </div>

    <style>
        @media print {
            .btn, .navbar, .sidebar, .navbar-brand, nav {
                display: none !important;
            }
            
            .wrapper {
                margin-left: 0 !important;
            }
            
            body {
                background: white;
            }
        }
    </style>
@endsection