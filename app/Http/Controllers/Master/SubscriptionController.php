<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('tenant')->orderBy('renews_on')->get();

        $stats = [
            'active' => $subscriptions->where('status', 'active')->count(),
            'mrr' => (float) $subscriptions->where('status', 'active')->sum('monthly_price'),
            'renewing_soon' => $subscriptions->filter(
                fn (Subscription $s) => $s->status === 'active' && $s->renews_on->lte(now()->addDays(30))
            )->count(),
        ];

        return view('crm.master.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function create()
    {
        return view('crm.master.subscriptions.create', [
            'tenants' => Tenant::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Subscription::create($this->validated($request));

        return redirect()->route('master.subscriptions.index')
            ->with('success', 'Subscription plan created.');
    }

    public function edit(Subscription $subscription)
    {
        return view('crm.master.subscriptions.edit', [
            'subscription' => $subscription,
            'tenants' => Tenant::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $subscription->update($this->validated($request));

        return redirect()->route('master.subscriptions.index')
            ->with('success', 'Subscription plan updated.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('master.subscriptions.index')
            ->with('success', 'Subscription plan removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan' => 'required|string|max:100',
            'monthly_price' => 'required|numeric|min:0',
            'started_on' => 'required|date',
            'renews_on' => 'required|date|after_or_equal:started_on',
            'status' => 'required|in:active,past_due,cancelled',
            'features' => 'nullable|string',
        ]);
    }
}
