@php $promotion = $promotion ?? null; @endphp

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $promotion?->title) }}" required>
    </div>
    @if($branches->isNotEmpty())
        <div class="col-md-4 mb-3">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-select">
                <option value="">All branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $promotion?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-3 mb-3">
        <label class="form-label">Discount Type</label>
        <select name="discount_type" class="form-select">
            @foreach(['percentage', 'fixed'] as $type)
                <option value="{{ $type }}" @selected(old('discount_type', $promotion?->discount_type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Discount Value</label>
        <input type="number" step="0.01" name="discount_value" class="form-control"
               value="{{ old('discount_value', $promotion?->discount_value ?? 0) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Starts On</label>
        <input type="date" name="starts_on" class="form-control"
               value="{{ old('starts_on', $promotion?->starts_on?->toDateString() ?? now()->toDateString()) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Ends On</label>
        <input type="date" name="ends_on" class="form-control"
               value="{{ old('ends_on', $promotion?->ends_on?->toDateString() ?? now()->addMonth()->toDateString()) }}" required>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $promotion?->description) }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Implemented By</label>
        <input type="text" class="form-control" value="{{ $promotion?->implementer?->name ?? auth()->user()->name }}" disabled>
        <small class="text-muted">Auto-populated with the logged-in user.</small>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Reason for Implementation <span class="text-danger">*</span></label>
        <textarea name="reason_for_implementation" class="form-control" rows="3" required
                  placeholder="Explain the business purpose of this promotion (minimum 10 characters).">{{ old('reason_for_implementation', $promotion?->reason_for_implementation) }}</textarea>
    </div>
</div>

<div class="alert alert-info">
    Submitting this form sets the promotion status to <strong>Pending</strong>. A manager or admin must approve it before it becomes active.
</div>
