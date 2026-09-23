@extends('layouts.crm')

@section('title', 'Interactions')
@section('page-title', 'Customer Interaction Logs')

@section('content')
    <div class="row">
        @foreach($counts as $type => $count)
            <div class="col-md-4">
                <x-kpi-card :label="$type" :value="$count" icon="bi-chat-left-text"
                            :href="route('interactions.index', ['type' => $type])" hint="Filter log" />
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        @foreach(\App\Models\Interaction::TYPES as $type)
                            <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach(['open', 'in_progress', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('interactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('interactions.create') }}" class="btn btn-success ms-auto"><i class="bi bi-plus-circle"></i> Log Interaction</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Date</th><th>Guest</th><th>Type</th><th>Channel</th><th>Subject</th><th>Logged By</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($interactions as $interaction)
                    <tr>
                        <td>{{ $interaction->created_at->format('M d, Y') }}</td>
                        <td>{{ $interaction->guest->full_name }}</td>
                        <td><span class="badge badge-info">{{ $interaction->type }}</span></td>
                        <td>{{ ucfirst($interaction->channel) }}</td>
                        <td>{{ $interaction->subject }}</td>
                        <td>{{ $interaction->logger?->name ?? '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $interaction->status)) }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('interactions.edit', $interaction) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('interactions.destroy', $interaction) }}" method="POST" onsubmit="return confirm('Delete this log?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No interactions logged.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $interactions->links() }}
@endsection
