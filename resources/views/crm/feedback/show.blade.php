@extends('layouts.crm')

@section('title', 'Feedback #'.$feedback->id)
@section('page-title', 'Feedback #'.$feedback->id)

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-3">
                <div>
                    <h4>{{ $feedback->guest->full_name }}</h4>
                    <p class="mb-1">{{ str_repeat('★', $feedback->rating) }} · {{ ucfirst($feedback->category) }}</p>
                    @if($feedback->reservation)
                        <p class="mb-1 text-muted">Reservation {{ $feedback->reservation->reference }}</p>
                    @endif
                    <span class="badge {{ $feedback->status === 'resolved' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($feedback->status) }}</span>
                </div>
                @if($feedback->status !== 'resolved')
                    <form action="{{ route('feedback.escalate', $feedback) }}" method="POST">
                        @csrf<button class="btn btn-outline-warning">Escalate to Action</button>
                    </form>
                @endif
            </div>
            <hr>
            <p class="mb-0">{{ $feedback->comment }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white"><h5 class="mb-0">Resolution</h5></div>
        <div class="card-body">
            <form action="{{ route('feedback.resolve', $feedback) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Resolution Notes</label>
                    <textarea name="resolution_notes" class="form-control" rows="3">{{ old('resolution_notes', $feedback->resolution_notes) }}</textarea>
                </div>
                <button class="btn btn-success" @disabled($feedback->status === 'resolved')>Mark Resolved</button>
                <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary">Back</a>
            </form>
        </div>
    </div>
@endsection
