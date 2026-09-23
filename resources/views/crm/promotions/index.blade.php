@extends('layouts.crm')

@section('title', 'Promotions')
@section('page-title', 'Promotions')

@section('content')
    <div class="row">
        @foreach($counts as $status => $count)
            <div class="col-md-4">
                <x-kpi-card :label="$status.' Promotions'" :value="$count" icon="bi-megaphone"
                            :href="route('promotions.index', ['status' => $status])" hint="Filter" />
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">Promotions require manager or admin approval before they become active.</p>
        <a href="{{ route('promotions.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Promotion</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Title</th><th>Discount</th><th>Period</th><th>Implemented By</th><th>Approved By</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($promotions as $promotion)
                    <tr>
                        <td><a href="{{ route('promotions.show', $promotion) }}">{{ $promotion->title }}</a></td>
                        <td>{{ $promotion->discount_type === 'percentage' ? $promotion->discount_value.'%' : '₱'.number_format($promotion->discount_value, 2) }}</td>
                        <td>{{ $promotion->starts_on->format('M d') }} – {{ $promotion->ends_on->format('M d, Y') }}</td>
                        <td>{{ $promotion->implementer?->name ?? '—' }}</td>
                        <td>{{ $promotion->approver?->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $promotion->statusBadge() }}">{{ $promotion->status }}</span>
                            @if($promotion->isActive())<span class="badge badge-info">Active</span>@endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('promotions.show', $promotion) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No promotions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $promotions->links() }}
@endsection
