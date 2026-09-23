@extends('layouts.crm')

@section('title', 'Edit Reservation')
@section('page-title', 'Edit Reservation')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reservations.update', $reservation) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.reservations._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
