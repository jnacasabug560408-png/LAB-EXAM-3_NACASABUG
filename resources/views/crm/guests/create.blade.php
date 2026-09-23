@extends('layouts.crm')

@section('title', 'Register Guest')
@section('page-title', 'Guest Registration')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('guests.store') }}" method="POST">
                @csrf
                @include('crm.guests._form')
                <button class="btn btn-primary">Save Guest</button>
                <a href="{{ route('guests.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
