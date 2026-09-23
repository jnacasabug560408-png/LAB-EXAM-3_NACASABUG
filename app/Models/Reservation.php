<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use BelongsToTenant;

    public const STATUSES = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];

    protected $fillable = [
        'tenant_id', 'branch_id', 'guest_id', 'reference', 'room_number', 'room_type',
        'check_in_date', 'check_out_date', 'checked_in_at', 'checked_out_at',
        'adults', 'children', 'total_amount', 'status', 'notes',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'confirmed' => 'badge-info',
            'checked_in' => 'badge-success',
            'checked_out' => 'badge-info',
            'cancelled' => 'badge-danger',
            default => 'badge-warning',
        };
    }
}
