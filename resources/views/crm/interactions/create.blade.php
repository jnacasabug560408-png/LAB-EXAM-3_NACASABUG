@extends('layouts.crm')

@section('title', 'Log Interaction')
@section('page-title', 'Log Customer Interaction')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('interactions.store') }}" method="POST">
                @csrf
                @include('crm.interactions._form')
                <button class="btn btn-primary">Save Log</button>
                <a href="{{ route('interactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
