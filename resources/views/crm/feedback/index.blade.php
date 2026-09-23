@extends('layouts.crm')

@section('title', 'Feedback')
@section('page-title', 'Guest Feedback')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-kpi-card label="All Feedback" :value="$counts['all']" icon="bi-chat-dots"
                        :href="route('feedback.index')" hint="View all" />
        </div>
        <div class="col-md-4">
            <x-kpi-card label="Unresolved" :value="$counts['unresolved']" icon="bi-exclamation-circle"
                        :href="route('feedback.index', ['status' => 'unresolved'])" hint="Needs attention" />
        </div>
        <div class="col-md-4">
            <x-kpi-card label="Resolved" :value="$counts['resolved']" icon="bi-check-circle"
                        :href="route('feedback.index', ['status' => 'resolved'])" hint="Closed" />
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="unresolved" @selected($status === 'unresolved')>Unresolved</option>
                        <option value="resolved" @selected($status === 'resolved')>Resolved</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">Any</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} star</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('feedback.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-circle"></i> Record Feedback</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Guest</th><th>Rating</th><th>Category</th><th>Comment</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($feedback as $item)
                    <tr>
                        <td>{{ $item->guest->full_name }}</td>
                        <td>{{ str_repeat('★', $item->rating) }}</td>
                        <td>{{ ucfirst($item->category) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->comment, 70) }}</td>
                        <td><span class="badge {{ $item->status === 'resolved' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($item->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('feedback.show', $item) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @if($item->status !== 'resolved')
                                    <form action="{{ route('feedback.escalate', $item) }}" method="POST">
                                        @csrf<button class="btn btn-sm btn-outline-warning">Escalate</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No feedback found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $feedback->links() }}
@endsection
