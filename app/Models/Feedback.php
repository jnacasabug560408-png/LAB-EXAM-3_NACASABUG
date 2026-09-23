<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use BelongsToTenant;

    protected $table = 'feedback';

    protected $fillable = [
        'tenant_id', 'branch_id', 'guest_id', 'reservation_id', 'rating',
        'category', 'comment', 'status', 'resolution_notes',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
