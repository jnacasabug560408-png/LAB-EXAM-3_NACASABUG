<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
    }

    public function boot(): void
    {
        View::composer('layouts.crm', function ($view) {
            $context = app(TenantContext::class);

            $view->with([
                'tenantContext' => $context,
                'availableTenants' => $context->isMaster()
                    ? Tenant::orderBy('name')->get()
                    : collect(),
                'availableBranches' => $context->supportsBranching()
                    ? Branch::where('status', 'active')->orderBy('name')->get()
                    : collect(),
            ]);
        });
    }
}
