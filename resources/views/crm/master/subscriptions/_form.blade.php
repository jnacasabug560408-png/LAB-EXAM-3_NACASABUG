@php $subscription = $subscription ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tenant</label>
        <select name="tenant_id" class="form-select" required>
            @foreach($tenants as $tenant)
                <option value="{{ $tenant->id }}" @selected(old('tenant_id', $subscription?->tenant_id) == $tenant->id)>
                    {{ $tenant->name }} ({{ $tenant->code }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Plan</label>
        <input type="text" name="plan" class="form-control" value="{{ old('plan', $subscription?->plan) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Monthly Price</label>
        <input type="number" step="0.01" name="monthly_price" class="form-control"
               value="{{ old('monthly_price', $subscription?->monthly_price ?? 0) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Started On</label>
        <input type="date" name="started_on" class="form-control"
               value="{{ old('started_on', $subscription?->started_on?->toDateString() ?? now()->toDateString()) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Renews On</label>
        <input type="date" name="renews_on" class="form-control"
               value="{{ old('renews_on', $subscription?->renews_on?->toDateString() ?? now()->addYear()->toDateString()) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active', 'past_due', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(old('status', $subscription?->status ?? 'active') === $status)>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label">Feature Access <small class="text-muted">(comma separated)</small></label>
        <input type="text" name="features" class="form-control" value="{{ old('features', $subscription?->features) }}"
               placeholder="Transactions, Business Intelligence, Multi-branch">
    </div>
</div>
