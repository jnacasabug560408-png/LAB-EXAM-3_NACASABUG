@extends('layouts.crm')

@section('title', 'Edit Promotion')
@section('page-title', 'Edit Promotion')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('promotions.update', $promotion) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.promotions._form')
                <button class="btn btn-primary">Save &amp; Resubmit</button>
                <a href="{{ route('promotions.show', $promotion) }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
