@php $guest = $guest ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $guest?->first_name) }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $guest?->last_name) }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $guest?->email) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $guest?->phone) }}">
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $guest?->address) }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">ID Number</label>
        <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $guest?->id_number) }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Nationality</label>
        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $guest?->nationality) }}">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(['active', 'inactive'] as $status)
                <option value="{{ $status }}" @selected(old('status', $guest?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    @if($branches->isNotEmpty())
        <div class="col-md-6 mb-3">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-select">
                <option value="">Unassigned</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $guest?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-12 mb-3">
        <label class="form-label">Preferences / Notes</label>
        <textarea name="preferences" class="form-control" rows="3">{{ old('preferences', $guest?->preferences) }}</textarea>
    </div>
</div>
