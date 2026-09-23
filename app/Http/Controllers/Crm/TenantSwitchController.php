<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function switchTenant(Request $request)
    {
        abort_unless($request->user()->isMaster(), 403);

        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $request->session()->forget('active_branch_id');

        if (empty($validated['tenant_id'])) {
            $request->session()->forget('active_tenant_id');

            return back()->with('success', 'Switched to the Master platform perspective.');
        }

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $request->session()->put('active_tenant_id', $tenant->id);

        return back()->with('success', "Now viewing {$tenant->name}.");
    }

    public function switchBranch(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if (empty($validated['branch_id'])) {
            $request->session()->forget('active_branch_id');

            return back()->with('success', 'Showing data for all branches.');
        }

        $branch = Branch::findOrFail($validated['branch_id']);
        $request->session()->put('active_branch_id', $branch->id);

        return back()->with('success', "Now filtering by {$branch->name}.");
    }
}
