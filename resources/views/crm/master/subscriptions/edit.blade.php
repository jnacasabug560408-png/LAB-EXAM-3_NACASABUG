@extends('layouts.crm')

@section('title', 'Edit Subscription')
@section('page-title', 'Edit Subscription Plan')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('master.subscriptions.update', $subscription) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.master.subscriptions._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('master.subscriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
