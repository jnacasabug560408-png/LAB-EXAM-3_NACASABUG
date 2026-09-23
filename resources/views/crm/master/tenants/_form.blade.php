@php $tenant = $tenant ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tenant Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $tenant?->name) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $tenant?->code) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Tier</label>
        <select name="tier" class="form-select">
            @foreach(['starter', 'standard', 'enterprise'] as $tier)
                <option value="{{ $tier }}" @selected(old('tier', $tenant?->tier) === $tier)>{{ ucfirst($tier) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Contact Email</label>
        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $tenant?->contact_email) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $tenant?->contact_phone) }}">
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $tenant?->description) }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active', 'suspended'] as $status)
                <option value="{{ $status }}" @selected(old('status', $tenant?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="supports_branching" value="1" class="form-check-input" id="supports_branching"
                   @checked(old('supports_branching', $tenant?->supports_branching))>
            <label class="form-check-label" for="supports_branching">Enable multi-branch support</label>
        </div>
    </div>
</div>
