@php $action = $action ?? null; @endphp

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $action?->title) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
            @foreach(\App\Models\Action::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $action?->type) === $type)>{{ ucwords(str_replace('-', ' ', $type)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Guest <small class="text-muted">(optional)</small></label>
        <select name="guest_id" class="form-select">
            <option value="">None</option>
            @foreach($guests as $guest)
                <option value="{{ $guest->id }}" @selected(old('guest_id', $action?->guest_id) == $guest->id)>{{ $guest->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Assign To</label>
        <select name="assigned_to" class="form-select">
            <option value="">Unassigned</option>
            @foreach($staff as $member)
                <option value="{{ $member->id }}" @selected(old('assigned_to', $action?->assigned_to) == $member->id)>
                    {{ $member->name }} ({{ ucfirst($member->role) }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $action?->due_date?->toDateString()) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-select">
            @foreach(['low', 'medium', 'high'] as $priority)
                <option value="{{ $priority }}" @selected(old('priority', $action?->priority ?? 'medium') === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(\App\Models\Action::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $action?->status ?? 'Open') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $action?->description) }}</textarea>
    </div>
</div>
