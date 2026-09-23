<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Sale;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(protected TenantContext $context) {}

    public function index()
    {
        abort_unless($this->context->supportsBranching(), 403, 'Multi-branch support is not enabled for this tenant.');

        $branches = Branch::orderBy('name')->get()->map(function (Branch $branch) {
            $branch->reservations_total = Reservation::withoutGlobalScope('tenant')
                ->where('branch_id', $branch->id)->count();
            $branch->guests_total = Guest::withoutGlobalScope('tenant')
                ->where('branch_id', $branch->id)->count();
            $branch->revenue_total = (float) Sale::withoutGlobalScope('tenant')
                ->where('branch_id', $branch->id)->where('status', 'completed')->sum('amount');

            return $branch;
        });

        return view('crm.branches.index', compact('branches'));
    }

    public function create()
    {
        abort_unless($this->context->supportsBranching(), 403);

        return view('crm.branches.create');
    }

    public function store(Request $request)
    {
        abort_unless($this->context->supportsBranching(), 403);

        Branch::create($this->validated($request));

        return redirect()->route('branches.index')->with('success', 'Branch created.');
    }

    public function edit(Branch $branch)
    {
        return view('crm.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($this->validated($request, $branch));

        return redirect()->route('branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Request $request, Branch $branch)
    {
        if ($request->session()->get('active_branch_id') == $branch->id) {
            $request->session()->forget('active_branch_id');
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch removed.');
    }

    protected function validated(Request $request, ?Branch $branch = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'total_rooms' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);
    }
}
