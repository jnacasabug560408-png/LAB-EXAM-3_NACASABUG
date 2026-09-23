@php $branch = $branch ?? null; @endphp

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Branch Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $branch?->name) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $branch?->code) }}" required>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $branch?->address) }}">
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label">Branch Manager</label>
        <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $branch?->manager_name) }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Total Rooms</label>
        <input type="number" name="total_rooms" class="form-control" min="0" value="{{ old('total_rooms', $branch?->total_rooms ?? 0) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active', 'inactive'] as $status)
                <option value="{{ $status }}" @selected(old('status', $branch?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
</div>
