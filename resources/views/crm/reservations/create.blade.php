@extends('layouts.crm')

@section('title', 'New Reservation')
@section('page-title', 'Create Reservation')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf
                @include('crm.reservations._form')
                <button class="btn btn-primary">Create Reservation</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
