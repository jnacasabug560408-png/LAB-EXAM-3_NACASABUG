@extends('layouts.app')

@section('title', 'Books')
@section('page-title', 'Book Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Books</h2>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Book
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($books->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ISBN</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Total Copies</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                            <tr>
                                <td>{{ $book->isbn }}</td>
                                <td>
                                    <strong>{{ $book->title }}</strong>
                                </td>
                                <td>{{ $book->author }}</td>
                                <td>
                                    <span class="badge" style="background-color: #d1ecf1; color: #0c5460;">
                                        {{ $book->category }}
                                    </span>
                                </td>
                                <td>{{ $book->total_copies }}</td>
                                <td>
                                    @if($book->available_copies > 0)
                                        <span class="badge badge-success">{{ $book->available_copies }}</span>
                                    @else
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('books.destroy', $book) }}" method="POST" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i> No books found. <a href="{{ route('books.create') }}">Add one now</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $books->links() }}
    </div>
@endsection