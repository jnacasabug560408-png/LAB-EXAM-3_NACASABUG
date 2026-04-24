@extends('layouts.app')

@section('title', 'New Borrowing')
@section('page-title', 'Create Borrowing Transaction')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Borrowing Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('borrowings.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                            <select class="form-select @error('member_id') is-invalid @enderror" 
                                    id="member_id" name="member_id" required onchange="updateMemberInfo()">
                                <option value="">Select a member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                        {{ $member->member_id }} - {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="member-info" class="alert alert-info d-none">
                            <p class="mb-1"><strong>Email:</strong> <span id="member-email"></span></p>
                            <p class="mb-1"><strong>Phone:</strong> <span id="member-phone"></span></p>
                            <p class="mb-0"><strong>Penalties:</strong> <span id="member-penalties">₱0.00</span></p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="book_id" class="form-label">Book <span class="text-danger">*</span></label>
                            <select class="form-select @error('book_id') is-invalid @enderror" 
                                    id="book_id" name="book_id" required onchange="updateBookInfo()">
                                <option value="">Select a book</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)
                                            data-author="{{ $book->author }}" data-available="{{ $book->available_copies }}">
                                        {{ $book->title }} ({{ $book->available_copies }} available)
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="book-info" class="alert alert-info d-none">
                            <p class="mb-1"><strong>Author:</strong> <span id="book-author"></span></p>
                            <p class="mb-0"><strong>Available Copies:</strong> <span id="book-available"></span></p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity', 1) }}" 
                                   placeholder="Number of copies" required min="1">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="borrow_date" class="form-label">Borrow Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('borrow_date') is-invalid @enderror" 
                                   id="borrow_date" name="borrow_date" value="{{ old('borrow_date', now()->format('Y-m-d')) }}" required>
                            @error('borrow_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Standard borrow period: 14 days</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Create Borrowing
                            </button>
                            <a href="{{ route('borrowings.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const membersData = {!! json_encode($members->map(fn($m) => [
            'id' => $m->id,
            'email' => $m->email,
            'phone' => $m->phone,
            'penalties' => $m->penalties()->where('payment_status', 'unpaid')->sum('penalty_amount')
        ])->keyBy('id')) !!};

        function updateMemberInfo() {
            const memberSelect = document.getElementById('member_id');
            const memberInfo = document.getElementById('member-info');
            const selectedValue = memberSelect.value;

            if (selectedValue && membersData[selectedValue]) {
                const data = membersData[selectedValue];
                document.getElementById('member-email').textContent = data.email;
                document.getElementById('member-phone').textContent = data.phone;
                document.getElementById('member-penalties').textContent = '₱' + data.penalties.toFixed(2);
                memberInfo.classList.remove('d-none');
            } else {
                memberInfo.classList.add('d-none');
            }
        }

        function updateBookInfo() {
            const bookSelect = document.getElementById('book_id');
            const bookInfo = document.getElementById('book-info');
            const selectedOption = bookSelect.options[bookSelect.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('book-author').textContent = selectedOption.getAttribute('data-author');
                document.getElementById('book-available').textContent = selectedOption.getAttribute('data-available');
                bookInfo.classList.remove('d-none');
            } else {
                bookInfo.classList.add('d-none');
            }
        }

        // Load member and book info on page load if values exist
        updateMemberInfo();
        updateBookInfo();
    </script>
@endsection