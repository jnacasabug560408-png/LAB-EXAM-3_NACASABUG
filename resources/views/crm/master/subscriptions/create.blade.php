@extends('layouts.crm')

@section('title', 'New Subscription')
@section('page-title', 'Create Subscription Plan')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('master.subscriptions.store') }}" method="POST">
                @csrf
                @include('crm.master.subscriptions._form')
                <button class="btn btn-primary">Create Plan</button>
                <a href="{{ route('master.subscriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
