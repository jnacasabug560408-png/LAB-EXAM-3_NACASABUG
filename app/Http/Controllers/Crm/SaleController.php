<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with('guest', 'branch', 'recorder')
            ->latest('sold_at')
            ->paginate(15)
            ->withQueryString();

        $totals = [
            'today' => (float) Sale::where('status', 'completed')->whereDate('sold_at', today())->sum('amount'),
            'month' => (float) Sale::where('status', 'completed')->where('sold_at', '>=', now()->startOfMonth())->sum('amount'),
            'transactions' => Sale::count(),
        ];

        return view('crm.sales.index', compact('sales', 'totals'));
    }

    public function create()
    {
        return view('crm.sales.create', [
            'guests' => Guest::orderBy('first_name')->get(),
            'reservations' => Reservation::with('guest')->active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['amount'] = $data['quantity'] * $data['unit_price'];
        $data['reference'] = 'SAL-'.strtoupper(Str::random(8));
        $data['recorded_by'] = $request->user()->id;
        $data['sold_at'] = $data['sold_at'] ?? now();

        Sale::create($data);

        return redirect()->route('sales.index')->with('success', 'Point-of-sale entry recorded.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale entry removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'guest_id' => 'nullable|exists:guests,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'status' => 'required|in:completed,pending,refunded',
            'sold_at' => 'nullable|date',
        ]);
    }
}
