@extends('layouts.crm')

@section('title', 'New Promotion')
@section('page-title', 'Create Promotion')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('promotions.store') }}" method="POST">
                @csrf
                @include('crm.promotions._form')
                <button class="btn btn-primary">Submit for Approval</button>
                <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
