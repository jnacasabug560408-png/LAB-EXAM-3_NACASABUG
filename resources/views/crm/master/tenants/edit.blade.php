@extends('layouts.crm')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('master.tenants.update', $tenant) }}" method="POST">
                @csrf @method('PUT')
                @include('crm.master.tenants._form')
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ route('master.tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
