<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    use BelongsToTenant;

    public const STATUSES = ['Open', 'In Progress', 'Resolved'];

    public const TYPES = ['guest-issue', 'feedback-follow-up', 'room-maintenance', 'housekeeping'];

    protected $fillable = [
        'tenant_id', 'branch_id', 'guest_id', 'feedback_id', 'assigned_to', 'created_by',
        'title', 'description', 'type', 'priority', 'status', 'due_date', 'resolved_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'Resolved' => 'badge-success',
            'In Progress' => 'badge-info',
            default => 'badge-warning',
        };
    }
}
