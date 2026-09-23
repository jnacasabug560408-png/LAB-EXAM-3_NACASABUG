<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $context = app(TenantContext::class);

            if ($context->tenantId()) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $context->tenantId());
            } elseif ($context->isRestricted()) {
                $builder->whereRaw('1 = 0');
            }

            if ($context->branchId() && $builder->getModel()->usesBranchScope()) {
                $builder->where($builder->getModel()->getTable().'.branch_id', $context->branchId());
            }
        });

        static::creating(function ($model) {
            $context = app(TenantContext::class);

            if (! $model->tenant_id && $context->tenantId()) {
                $model->tenant_id = $context->tenantId();
            }

            if ($model->usesBranchScope() && ! $model->branch_id && $context->branchId()) {
                $model->branch_id = $context->branchId();
            }
        });
    }

    public function usesBranchScope(): bool
    {
        return in_array('branch_id', $this->getFillable(), true);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
