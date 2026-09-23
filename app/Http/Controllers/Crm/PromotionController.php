<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $promotions = Promotion::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('implementer', 'approver', 'branch')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = collect(Promotion::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => Promotion::where('status', $status)->count()])
            ->all();

        return view('crm.promotions.index', compact('promotions', 'counts'));
    }

    public function create()
    {
        return view('crm.promotions.create', ['branches' => $this->branches()]);
    }

    public function store(Request $request)
    {
        $promotion = Promotion::create($this->validated($request) + [
            'implemented_by' => $request->user()->id,
            'status' => 'Pending',
        ]);

        return redirect()->route('promotions.show', $promotion)
            ->with('success', 'Promotion submitted for approval.');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('implementer', 'approver', 'branch');

        return view('crm.promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion)
    {
        return view('crm.promotions.edit', [
            'promotion' => $promotion,
            'branches' => $this->branches(),
        ]);
    }

    public function update(Request $request, Promotion $promotion)
    {
        // Editing an approved or rejected promotion sends it back through approval.
        $promotion->update($this->validated($request) + [
            'status' => 'Pending',
            'approved_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
        ]);

        return redirect()->route('promotions.show', $promotion)
            ->with('success', 'Promotion updated and returned to Pending approval.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'Promotion deleted.');
    }

    public function approve(Request $request, Promotion $promotion)
    {
        return $this->review($request, $promotion, 'Approved');
    }

    public function reject(Request $request, Promotion $promotion)
    {
        return $this->review($request, $promotion, 'Rejected');
    }

    protected function review(Request $request, Promotion $promotion, string $status)
    {
        abort_unless($request->user()->canApprovePromotions(), 403, 'Only managers and admins can review promotions.');

        $validated = $request->validate([
            'review_notes' => $status === 'Rejected' ? 'required|string' : 'nullable|string',
        ]);

        $promotion->update([
            'status' => $status,
            'approved_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return back()->with('success', "Promotion {$status} by {$request->user()->name}.");
    }

    protected function branches()
    {
        return Branch::where('status', 'active')->orderBy('name')->get();
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after_or_equal:starts_on',
            'reason_for_implementation' => 'required|string|min:10',
        ]);
    }
}
