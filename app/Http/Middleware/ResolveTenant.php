<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(protected TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        $this->context->setMaster($user->isMaster());

        $tenant = $user->isMaster()
            ? $this->selectedTenant($request)
            : $user->tenant;

        if (! $user->isMaster() && $tenant && ! $tenant->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your organization account is suspended. Please contact the platform administrator.',
            ]);
        }

        $this->context->setTenant($tenant);
        $this->context->setRestricted(! $user->isMaster() && ! $tenant);
        $this->context->setBranch($this->selectedBranch($request, $tenant));

        return $next($request);
    }

    protected function selectedTenant(Request $request): ?Tenant
    {
        $tenantId = $request->session()->get('active_tenant_id');

        if (! $tenantId) {
            return null;
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $request->session()->forget('active_tenant_id');
        }

        return $tenant;
    }

    protected function selectedBranch(Request $request, ?Tenant $tenant): ?Branch
    {
        $branchId = $request->session()->get('active_branch_id');

        if (! $branchId || ! $tenant || ! $tenant->supports_branching) {
            return null;
        }

        $branch = Branch::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->find($branchId);

        if (! $branch) {
            $request->session()->forget('active_branch_id');
        }

        return $branch;
    }
}
