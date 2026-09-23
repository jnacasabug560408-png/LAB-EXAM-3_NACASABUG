@extends('layouts.crm')

@section('title', 'New Branch')
@section('page-title', 'Create Branch')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('branches.store') }}" method="POST">
                @csrf
                @include('crm.branches._form')
                <button class="btn btn-primary">Create Branch</button>
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
