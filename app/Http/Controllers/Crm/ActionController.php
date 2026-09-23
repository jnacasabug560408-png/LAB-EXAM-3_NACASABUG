<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Guest;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function __construct(protected TenantContext $context) {}

    public function index(Request $request)
    {
        $query = fn (string $status) => Action::with('assignee', 'guest')
            ->where('status', $status)
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->latest()
            ->get();

        $board = collect(Action::STATUSES)->mapWithKeys(fn ($status) => [$status => $query($status)]);

        return view('crm.actions.index', [
            'board' => $board,
            'counts' => $board->map->count(),
        ]);
    }

    public function create()
    {
        return view('crm.actions.create', $this->formData());
    }

    public function store(Request $request)
    {
        Action::create($this->validated($request) + ['created_by' => $request->user()->id]);

        return redirect()->route('actions.index')->with('success', 'Action logged.');
    }

    public function edit(Action $action)
    {
        return view('crm.actions.edit', $this->formData() + ['action' => $action]);
    }

    public function update(Request $request, Action $action)
    {
        $data = $this->validated($request);
        $data['resolved_at'] = $data['status'] === 'Resolved' ? ($action->resolved_at ?? now()) : null;

        $action->update($data);

        return redirect()->route('actions.index')->with('success', 'Action updated.');
    }

    public function updateStatus(Request $request, Action $action)
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', Action::STATUSES),
        ]);

        $action->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'Resolved' ? now() : null,
        ]);

        return back()->with('success', "Action moved to {$validated['status']}.");
    }

    public function destroy(Action $action)
    {
        $action->delete();

        return redirect()->route('actions.index')->with('success', 'Action removed.');
    }

    protected function formData(): array
    {
        return [
            'guests' => Guest::orderBy('first_name')->get(),
            'staff' => User::where('tenant_id', $this->context->tenantId())->orderBy('name')->get(),
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'guest_id' => 'nullable|exists:guests,id',
            'assigned_to' => 'nullable|exists:users,id',
            'type' => 'required|in:'.implode(',', Action::TYPES),
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:'.implode(',', Action::STATUSES),
            'due_date' => 'nullable|date',
        ]);
    }
}
