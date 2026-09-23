<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('reference', 'like', $term)
                    ->orWhere('room_number', 'like', $term)
                    ->orWhereHas('guest', fn ($g) => $g->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term));
            })
            ->with('guest', 'branch')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statusCounts = collect(Reservation::STATUSES)
            ->mapWithKeys(fn ($status) => [$status => Reservation::where('status', $status)->count()])
            ->all();

        return view('crm.reservations.index', compact('reservations', 'statusCounts'));
    }

    public function create()
    {
        return view('crm.reservations.create', [
            'guests' => $this->guests(),
            'branches' => $this->branches(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['reference'] = 'RES-'.strtoupper(Str::random(8));

        $reservation = Reservation::create($data);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', "Reservation {$reservation->reference} created.");
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('guest', 'branch', 'sales');

        return view('crm.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        return view('crm.reservations.edit', [
            'reservation' => $reservation,
            'guests' => $this->guests(),
            'branches' => $this->branches(),
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update($this->validated($request));

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted.');
    }

    public function checkIn(Reservation $reservation)
    {
        if (in_array($reservation->status, ['checked_out', 'cancelled'], true)) {
            return back()->with('error', 'This reservation can no longer be checked in.');
        }

        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return back()->with('success', "{$reservation->guest->full_name} checked in to room {$reservation->room_number}.");
    }

    public function checkOut(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out.');
        }

        $reservation->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);

        Sale::create([
            'branch_id' => $reservation->branch_id,
            'guest_id' => $reservation->guest_id,
            'reservation_id' => $reservation->id,
            'recorded_by' => $request->user()->id,
            'reference' => 'SAL-'.strtoupper(Str::random(8)),
            'description' => "Room charge for reservation {$reservation->reference}",
            'category' => 'room',
            'quantity' => 1,
            'unit_price' => $reservation->total_amount,
            'amount' => $reservation->total_amount,
            'payment_method' => 'card',
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        return back()->with('success', 'Guest checked out and room charge posted to sales.');
    }

    protected function guests()
    {
        return Guest::orderBy('first_name')->get();
    }

    protected function branches()
    {
        return Branch::where('status', 'active')->orderBy('name')->get();
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'branch_id' => 'nullable|exists:branches,id',
            'room_number' => 'required|string|max:20',
            'room_type' => 'required|string|max:50',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'required|integer|min:0|max:20',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:'.implode(',', Reservation::STATUSES),
            'notes' => 'nullable|string',
        ]);
    }
}
