@extends('layouts.app')

@section('title', 'Edit Borrowing')
@section('page-title', 'Edit Borrowing Transaction')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Borrowing Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info mb-3">
                            <strong>Member:</strong> {{ $borrowing->member->name }}<br>
                            <strong>Book:</strong> {{ $borrowing->book->title }}<br>
                            <strong>Current Status:</strong> 
                            @if($borrowing->status == 'borrowed')
                                <span class="badge badge-info">Borrowed</span>
                            @elseif($borrowing->status == 'returned')
                                <span class="badge badge-success">Returned</span>
                            @else
                                <span class="badge badge-danger">Overdue</span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="borrow_date" class="form-label">Borrow Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('borrow_date') is-invalid @enderror" 
                                   id="borrow_date" name="borrow_date" value="{{ old('borrow_date', $borrowing->borrow_date->format('Y-m-d')) }}" required>
                            @error('borrow_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', $borrowing->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Update Borrowing
                            </button>
                            <a href="{{ route('borrowings.show', $borrowing) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection