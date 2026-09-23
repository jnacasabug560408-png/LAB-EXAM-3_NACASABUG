@php $reservation = $reservation ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Guest</label>
        <select name="guest_id" class="form-select" required>
            <option value="">Select guest</option>
            @foreach($guests as $guest)
                <option value="{{ $guest->id }}" @selected(old('guest_id', $reservation?->guest_id) == $guest->id)>{{ $guest->full_name }}</option>
            @endforeach
        </select>
    </div>
    @if($branches->isNotEmpty())
        <div class="col-md-6 mb-3">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-select">
                <option value="">Unassigned</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $reservation?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-3 mb-3">
        <label class="form-label">Room Number</label>
        <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $reservation?->room_number) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Room Type</label>
        <select name="room_type" class="form-select">
            @foreach(['standard', 'deluxe', 'suite', 'family'] as $type)
                <option value="{{ $type }}" @selected(old('room_type', $reservation?->room_type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Check-In Date</label>
        <input type="date" name="check_in_date" class="form-control"
               value="{{ old('check_in_date', $reservation?->check_in_date?->toDateString() ?? now()->toDateString()) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Check-Out Date</label>
        <input type="date" name="check_out_date" class="form-control"
               value="{{ old('check_out_date', $reservation?->check_out_date?->toDateString() ?? now()->addDay()->toDateString()) }}" required>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Adults</label>
        <input type="number" name="adults" class="form-control" min="1" value="{{ old('adults', $reservation?->adults ?? 1) }}" required>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Children</label>
        <input type="number" name="children" class="form-control" min="0" value="{{ old('children', $reservation?->children ?? 0) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Total Amount</label>
        <input type="number" step="0.01" name="total_amount" class="form-control"
               value="{{ old('total_amount', $reservation?->total_amount ?? 0) }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach(\App\Models\Reservation::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $reservation?->status ?? 'pending') === $status)>
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $reservation?->notes) }}</textarea>
    </div>
</div>
