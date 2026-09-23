<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use BelongsToTenant;

    public const STATUSES = ['Pending', 'Approved', 'Rejected'];

    protected $fillable = [
        'tenant_id', 'branch_id', 'title', 'description', 'discount_type', 'discount_value',
        'starts_on', 'ends_on', 'implemented_by', 'reason_for_implementation',
        'approved_by', 'reviewed_at', 'review_notes', 'status',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'reviewed_at' => 'datetime',
        'discount_value' => 'decimal:2',
    ];

    public function implementer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'implemented_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'Approved'
            && $this->starts_on <= now()->startOfDay()
            && $this->ends_on >= now()->startOfDay();
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'Approved' => 'badge-success',
            'Rejected' => 'badge-danger',
            default => 'badge-warning',
        };
    }
}
