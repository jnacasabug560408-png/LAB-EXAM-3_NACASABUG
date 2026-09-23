@extends('layouts.crm')

@section('title', 'New Tenant')
@section('page-title', 'Create Tenant')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('master.tenants.store') }}" method="POST">
                @csrf
                @include('crm.master.tenants._form')
                <button class="btn btn-primary">Create Tenant</button>
                <a href="{{ route('master.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
