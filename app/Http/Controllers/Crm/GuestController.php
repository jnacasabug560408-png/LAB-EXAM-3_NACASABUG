<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $guests = Guest::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where(fn ($sub) => $sub->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term));
            })
            ->with('branch')
            ->withCount('reservations')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('crm.guests.index', compact('guests'));
    }

    public function create()
    {
        return view('crm.guests.create', ['branches' => $this->branches()]);
    }

    public function store(Request $request)
    {
        $guest = Guest::create($this->validated($request));

        return redirect()->route('guests.show', $guest)
            ->with('success', 'Guest profile registered.');
    }

    public function show(Guest $guest)
    {
        $guest->load([
            'reservations' => fn ($q) => $q->latest(),
            'feedback' => fn ($q) => $q->latest(),
            'interactions' => fn ($q) => $q->latest(),
            'sales' => fn ($q) => $q->latest('sold_at'),
        ]);

        return view('crm.guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('crm.guests.edit', ['guest' => $guest, 'branches' => $this->branches()]);
    }

    public function update(Request $request, Guest $guest)
    {
        $guest->update($this->validated($request));

        return redirect()->route('guests.show', $guest)->with('success', 'Guest profile updated.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'Guest profile deleted.');
    }

    protected function branches()
    {
        return Branch::where('status', 'active')->orderBy('name')->get();
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'preferences' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
        ]);
    }
}
