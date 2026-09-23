@extends('layouts.crm')

@section('title', $promotion->title)
@section('page-title', 'Promotion Review')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-3">
                <div>
                    <h4>{{ $promotion->title }}</h4>
                    <p class="mb-1 text-muted">
                        {{ $promotion->discount_type === 'percentage' ? $promotion->discount_value.'% off' : '₱'.number_format($promotion->discount_value, 2).' off' }}
                        · {{ $promotion->starts_on->format('M d, Y') }} – {{ $promotion->ends_on->format('M d, Y') }}
                    </p>
                    @if($promotion->branch)<span class="badge badge-info">{{ $promotion->branch->name }}</span>@endif
                    <span class="badge {{ $promotion->statusBadge() }}">{{ $promotion->status }}</span>
                    @if($promotion->isActive())<span class="badge badge-success">Currently Active</span>@endif
                </div>
                <div class="d-flex gap-2 align-items-start">
                    <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-outline-primary">Edit</a>
                    <form action="{{ route('promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('Delete this promotion?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>
            <hr>
            <p>{{ $promotion->description }}</p>
            <p class="mb-1"><strong>Implemented By:</strong> {{ $promotion->implementer?->name ?? '—' }}</p>
            <p class="mb-1"><strong>Reason for Implementation:</strong> {{ $promotion->reason_for_implementation }}</p>
            <p class="mb-1"><strong>Approved By:</strong> {{ $promotion->approver?->name ?? 'Awaiting review' }}</p>
            @if($promotion->reviewed_at)
                <p class="mb-1"><strong>Reviewed:</strong> {{ $promotion->reviewed_at->format('M d, Y H:i') }}</p>
            @endif
            @if($promotion->review_notes)
                <p class="mb-0"><strong>Review Notes:</strong> {{ $promotion->review_notes }}</p>
            @endif
        </div>
    </div>

    @if(auth()->user()->canApprovePromotions())
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Approval Decision</h5></div>
            <div class="card-body">
                @if($promotion->isPending())
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('promotions.approve', $promotion) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Approval Notes <small class="text-muted">(optional)</small></label>
                                    <textarea name="review_notes" class="form-control" rows="3"></textarea>
                                </div>
                                <button class="btn btn-success"><i class="bi bi-check-circle"></i> Approve Promotion</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('promotions.reject', $promotion) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea name="review_notes" class="form-control" rows="3" required></textarea>
                                </div>
                                <button class="btn btn-danger"><i class="bi bi-x-circle"></i> Reject Promotion</button>
                            </form>
                        </div>
                    </div>
                @else
                    <p class="mb-0 text-muted">This promotion was already {{ strtolower($promotion->status) }}. Edit it to send it back for approval.</p>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info">Only managers and admins can approve or reject promotions.</div>
    @endif

    <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">Back to promotions</a>
@endsection
