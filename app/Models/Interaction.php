<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    use BelongsToTenant;

    public const TYPES = ['Inquiry', 'Complaint', 'Special Request'];

    protected $fillable = [
        'tenant_id', 'branch_id', 'guest_id', 'logged_by', 'type',
        'channel', 'subject', 'details', 'status',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
