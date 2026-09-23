<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Interaction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function index(Request $request)
    {
        $interactions = Interaction::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('guest', 'logger')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = collect(Interaction::TYPES)
            ->mapWithKeys(fn ($type) => [$type => Interaction::where('type', $type)->count()])
            ->all();

        return view('crm.interactions.index', compact('interactions', 'counts'));
    }

    public function create()
    {
        return view('crm.interactions.create', [
            'guests' => Guest::orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Interaction::create($this->validated($request) + ['logged_by' => $request->user()->id]);

        return redirect()->route('interactions.index')->with('success', 'Customer interaction logged.');
    }

    public function edit(Interaction $interaction)
    {
        return view('crm.interactions.edit', [
            'interaction' => $interaction,
            'guests' => Guest::orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, Interaction $interaction)
    {
        $interaction->update($this->validated($request));

        return redirect()->route('interactions.index')->with('success', 'Interaction updated.');
    }

    public function destroy(Interaction $interaction)
    {
        $interaction->delete();

        return redirect()->route('interactions.index')->with('success', 'Interaction deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'type' => 'required|in:'.implode(',', Interaction::TYPES),
            'channel' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'details' => 'required|string',
            'status' => 'required|in:open,in_progress,closed',
        ]);
    }
}
