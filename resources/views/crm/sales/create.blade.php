@extends('layouts.crm')

@section('title', 'New POS Entry')
@section('page-title', 'New Point-of-Sale Entry')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest <small class="text-muted">(optional for walk-ins)</small></label>
                        <select name="guest_id" class="form-select">
                            <option value="">Walk-in</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" @selected(old('guest_id') == $guest->id)>{{ $guest->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Charge to Reservation <small class="text-muted">(optional)</small></label>
                        <select name="reservation_id" class="form-select">
                            <option value="">None</option>
                            @foreach($reservations as $reservation)
                                <option value="{{ $reservation->id }}" @selected(old('reservation_id') == $reservation->id)>
                                    {{ $reservation->reference }} — Room {{ $reservation->room_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            @foreach(['room', 'food', 'beverage', 'spa', 'laundry', 'other'] as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="{{ old('quantity', 1) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="{{ old('unit_price', 0) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Total</label>
                        <input type="text" id="total_preview" class="form-control" value="0.00" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            @foreach(['cash', 'card', 'gcash', 'bank-transfer', 'room-charge'] as $method)
                                <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ ucfirst($method) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['completed', 'pending', 'refunded'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'completed') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sold At</label>
                        <input type="datetime-local" name="sold_at" class="form-control"
                               value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
                <button class="btn btn-primary">Record Sale</button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const qty = document.getElementById('quantity');
    const price = document.getElementById('unit_price');
    const preview = document.getElementById('total_preview');

    function updateTotal() {
        preview.value = ((parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0)).toFixed(2);
    }

    qty.addEventListener('input', updateTotal);
    price.addEventListener('input', updateTotal);
    updateTotal();
</script>
@endpush
