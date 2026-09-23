@php $interaction = $interaction ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Guest</label>
        <select name="guest_id" class="form-select" required>
            <option value="">Select guest</option>
            @foreach($guests as $guest)
                <option value="{{ $guest->id }}" @selected(old('guest_id', $interaction?->guest_id) == $guest->id)>{{ $guest->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select">
            @foreach(\App\Models\Interaction::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $interaction?->type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Channel</label>
        <select name="channel" class="form-select">
            @foreach(['front-desk', 'phone', 'email', 'website', 'walk-in'] as $channel)
                <option value="{{ $channel }}" @selected(old('channel', $interaction?->channel) === $channel)>{{ ucfirst($channel) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" value="{{ old('subject', $interaction?->subject) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['open', 'in_progress', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $interaction?->status ?? 'open') === $status)>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Details</label>
        <textarea name="details" class="form-control" rows="4" required>{{ old('details', $interaction?->details) }}</textarea>
    </div>
</div>
