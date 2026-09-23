@extends('layouts.crm')

@section('title', 'Record Feedback')
@section('page-title', 'Record Guest Feedback')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest</label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Select guest</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" @selected(old('guest_id') == $guest->id)>{{ $guest->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reservation <small class="text-muted">(optional)</small></label>
                        <select name="reservation_id" class="form-select">
                            <option value="">None</option>
                            @foreach($reservations as $reservation)
                                <option value="{{ $reservation->id }}" @selected(old('reservation_id') == $reservation->id)>
                                    {{ $reservation->reference }} — {{ $reservation->guest->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} — {{ str_repeat('★', $i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            @foreach(['service', 'cleanliness', 'facilities', 'food', 'value'] as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="unresolved" @selected(old('status', 'unresolved') === 'unresolved')>Unresolved</option>
                            <option value="resolved" @selected(old('status') === 'resolved')>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="4" required>{{ old('comment') }}</textarea>
                    </div>
                </div>
                <button class="btn btn-primary">Save Feedback</button>
                <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
