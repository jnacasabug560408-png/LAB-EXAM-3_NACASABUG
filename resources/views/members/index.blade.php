@extends('layouts.app')

@section('title', 'Members')
@section('page-title', 'Member Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Members</h2>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Member
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['active'] }}</h3>
                <p>Active Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['inactive'] }}</h3>
                <p>Inactive Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <h3>{{ $stats['suspended'] }}</h3>
                <p>Suspended Members</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($members->count() > 0)
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Member Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td><code>{{ $member->member_id }}</code></td>
                                <td><strong>{{ $member->name }}</strong></td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->phone }}</td>
                                <td>
                                    @if($member->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($member->status == 'inactive')
                                        <span class="badge badge-info">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>{{ $member->membership_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('members.destroy', $member) }}" method="POST" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle"></i> No members found. <a href="{{ route('members.create') }}">Add one now</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $members->links() }}
    </div>
@endsection