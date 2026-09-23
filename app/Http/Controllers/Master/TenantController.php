<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'branches', 'guests', 'reservations'])
            ->with('subscription')
            ->orderBy('code')
            ->get();

        return view('crm.master.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('crm.master.tenants.create');
    }

    public function store(Request $request)
    {
        $tenant = Tenant::create($this->validated($request));

        return redirect()->route('master.tenants.index')
            ->with('success', "Tenant {$tenant->name} created.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'branches', 'guests', 'reservations']);
        $tenant->load('subscriptions', 'branches', 'users');

        return view('crm.master.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('crm.master.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($this->validated($request, $tenant));

        return redirect()->route('master.tenants.index')
            ->with('success', "Tenant {$tenant->name} updated.");
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('master.tenants.index')
            ->with('success', 'Tenant deleted.');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update([
            'status' => $tenant->isActive() ? 'suspended' : 'active',
        ]);

        return back()->with('success', "Tenant {$tenant->name} is now {$tenant->status}.");
    }

    protected function validated(Request $request, ?Tenant $tenant = null): array
    {
        $uniqueCode = 'unique:tenants,code'.($tenant ? ",{$tenant->id}" : '');

        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|max:20|{$uniqueCode}",
            'tier' => 'required|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'supports_branching' => 'nullable|boolean',
            'status' => 'required|in:active,suspended',
        ]) + ['supports_branching' => $request->boolean('supports_branching')];
    }
}
