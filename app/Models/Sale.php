<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'branch_id', 'guest_id', 'reservation_id', 'recorded_by', 'reference',
        'description', 'category', 'quantity', 'unit_price', 'amount',
        'payment_method', 'status', 'sold_at',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
