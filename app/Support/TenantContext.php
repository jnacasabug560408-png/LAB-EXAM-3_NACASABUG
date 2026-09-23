<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;

class TenantContext
{
    protected ?Tenant $tenant = null;

    protected ?Branch $branch = null;

    protected bool $master = false;

    /**
     * True when the current user is bound to a tenant that could not be resolved,
     * in which case tenant-scoped queries must return nothing.
     */
    protected bool $restricted = false;

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;

        if ($this->branch && $this->branch->tenant_id !== optional($tenant)->id) {
            $this->branch = null;
        }
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function tenantId(): ?int
    {
        return $this->tenant?->id;
    }

    public function setBranch(?Branch $branch): void
    {
        $this->branch = $branch;
    }

    public function branch(): ?Branch
    {
        return $this->branch;
    }

    public function branchId(): ?int
    {
        return $this->branch?->id;
    }

    public function setMaster(bool $master): void
    {
        $this->master = $master;
    }

    public function isMaster(): bool
    {
        return $this->master;
    }

    public function setRestricted(bool $restricted): void
    {
        $this->restricted = $restricted;
    }

    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    /**
     * A master admin with no tenant selected is viewing the whole platform.
     */
    public function isGlobalView(): bool
    {
        return $this->master && $this->tenant === null;
    }

    public function label(): string
    {
        if ($this->isGlobalView()) {
            return 'Master Perspective';
        }

        return $this->tenant?->name ?? 'No Tenant';
    }

    public function canSwitchTenants(?User $user = null): bool
    {
        return $this->master;
    }

    public function supportsBranching(): bool
    {
        return (bool) $this->tenant?->supports_branching;
    }
}
