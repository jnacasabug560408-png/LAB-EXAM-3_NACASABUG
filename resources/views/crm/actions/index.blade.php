@extends('layouts.crm')

@section('title', 'Actions')
@section('page-title', 'Action Board')

@section('content')
    <div class="row">
        @foreach($counts as $status => $count)
            <div class="col-md-4"><x-kpi-card :label="$status" :value="$count" icon="bi-kanban" /></div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Log, assign and resolve guest issues and operational tasks.</p>
        <a href="{{ route('actions.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Log Action</a>
    </div>

    <div class="row">
        @foreach($board as $status => $actions)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <h5 class="mb-0">{{ $status }}</h5>
                        <span class="badge badge-info">{{ $actions->count() }}</span>
                    </div>
                    <div class="card-body">
                        @forelse($actions as $action)
                            <div class="board-card">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $action->title }}</strong>
                                    <span class="badge {{ $action->priority === 'high' ? 'badge-danger' : ($action->priority === 'medium' ? 'badge-warning' : 'badge-info') }}">
                                        {{ ucfirst($action->priority) }}
                                    </span>
                                </div>
                                <small class="text-muted d-block">{{ $action->type }} · {{ $action->guest?->full_name ?? 'General' }}</small>
                                <small class="text-muted d-block">Assigned: {{ $action->assignee?->name ?? 'Unassigned' }}</small>
                                @if($action->due_date)
                                    <small class="text-muted d-block">Due {{ $action->due_date->format('M d, Y') }}</small>
                                @endif
                                <div class="d-flex gap-1 mt-2">
                                    <form action="{{ route('actions.status', $action) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach(\App\Models\Action::STATUSES as $option)
                                                <option value="{{ $option }}" @selected($action->status === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <a href="{{ route('actions.edit', $action) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Nothing here.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
