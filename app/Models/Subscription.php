<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id', 'plan', 'monthly_price', 'started_on', 'renews_on', 'status', 'features',
    ];

    protected $casts = [
        'started_on' => 'date',
        'renews_on' => 'date',
        'monthly_price' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function featureList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->features))));
    }

    public function isExpiringSoon(): bool
    {
        return $this->renews_on !== null && $this->renews_on->diffInDays(now(), false) > -30;
    }
}
