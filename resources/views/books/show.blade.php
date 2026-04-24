@extends('layouts.app')

@section('title', 'Book Details')
@section('page-title', 'Book Details')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $book->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>ISBN:</strong> {{ $book->isbn }}</p>
                            <p><strong>Author:</strong> {{ $book->author }}</p>
                            <p><strong>Category:</strong> {{ $book->category }}</p>
                            <p><strong>Publisher:</strong> {{ $book->publisher ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Publication Year:</strong> {{ $book->publication_year ?? 'N/A' }}</p>
                            <p><strong>Total Copies:</strong> {{ $book->total_copies }}</p>
                            <p><strong>Available Copies:</strong> 
                                @if($book->available_copies > 0)
                                    <span class="badge badge-success">{{ $book->available_copies }}</span>
                                @else
                                    <span class="badge badge-danger">Out of Stock</span>
                                @endif
                            </p>
                            <p><strong>Borrowed:</strong> {{ $book->total_copies - $book->available_copies }}</p>
                        </div>
                    </div>

                    @if($book->description)
                        <div class="mb-4">
                            <h6>Description:</h6>
                            <p>{{ $book->description }}</p>
                        </div>
                    @endif

                    <hr>

                    <div>
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Borrowing History</h5>
                </div>
                <div class="card-body p-0">
                    @if($book->borrowings()->count() > 0)
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->borrowings()->latest()->take(5)->get() as $borrowing)
                                    <tr>
                                        <td>{{ $borrowing->member->name }}</td>
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
        </div>
    </div>
@endsection