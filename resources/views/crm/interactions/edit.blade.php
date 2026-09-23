@extends('layouts.crm')

@section('title', 'Edit Interaction')
@section('page-title', 'Edit Customer Interaction')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('interactions.update', $interaction) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.interactions._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('interactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
