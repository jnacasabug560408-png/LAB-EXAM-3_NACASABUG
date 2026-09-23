@extends('layouts.crm')

@section('title', 'Edit Branch')
@section('page-title', 'Edit Branch')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('branches.update', $branch) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.branches._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
