@extends('layouts.crm')

@section('title', 'Edit Action')
@section('page-title', 'Edit Action')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('actions.update', $action) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.actions._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
