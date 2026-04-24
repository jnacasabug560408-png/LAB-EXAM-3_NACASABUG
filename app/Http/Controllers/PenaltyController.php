<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index()
    {
        $penalties = Penalty::with('member', 'borrowing')
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Penalty::count(),
            'unpaid' => Penalty::where('payment_status', 'unpaid')->count(),
            'paid' => Penalty::where('payment_status', 'paid')->count(),
            'waived' => Penalty::where('payment_status', 'waived')->count(),
            'total_amount' => Penalty::where('payment_status', 'unpaid')->sum('penalty_amount'),
        ];

        return view('penalties.index', compact('penalties', 'stats'));
    }

    public function show(Penalty $penalty)
    {
        return view('penalties.show', compact('penalty'));
    }

    public function markAsPaid(Penalty $penalty)
    {
        $penalty->markAsPaid();

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Penalty marked as paid!');
    }

    public function markAsUnpaid(Penalty $penalty)
    {
        $penalty->markAsUnpaid();

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Penalty marked as unpaid!');
    }

    public function waive(Request $request, Penalty $penalty)
    {
        $validated = $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $penalty->remarks = $validated['remarks'] ?? $penalty->remarks;
        $penalty->waive();

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Penalty waived!');
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();

        return redirect()->route('penalties.index')
            ->with('success', 'Penalty record deleted!');
    }
}