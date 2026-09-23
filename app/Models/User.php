<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLES = ['master', 'admin', 'manager', 'staff'];

    protected $fillable = [
        'name', 'email', 'password', 'tenant_id', 'branch_id', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isMaster(): bool
    {
        return $this->role === 'master';
    }

    public function canApprovePromotions(): bool
    {
        return in_array($this->role, ['master', 'admin', 'manager'], true);
    }
}
