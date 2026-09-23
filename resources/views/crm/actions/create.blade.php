@extends('layouts.crm')

@section('title', 'Log Action')
@section('page-title', 'Log Action')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('actions.store') }}" method="POST">
                @csrf
                @include('crm.actions._form')
                <button class="btn btn-primary">Save Action</button>
                <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
