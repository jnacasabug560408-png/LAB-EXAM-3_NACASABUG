@extends('layouts.crm')

@section('title', 'Edit Guest')
@section('page-title', 'Edit Guest Profile')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('guests.update', $guest) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.guests._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('guests.show', $guest) }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
