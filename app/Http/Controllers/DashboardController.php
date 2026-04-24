<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use App\Models\Penalty;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'members' => Member::where('status', 'active')->count(),
            'borrowed' => Borrowing::where('status', 'borrowed')->count(),
            'overdue' => Borrowing::where('status', 'overdue')->count(),
            'unpaid_penalties' => Penalty::where('payment_status', 'unpaid')->sum('penalty_amount'),
            'total_penalties_count' => Penalty::where('payment_status', 'unpaid')->count(),
        ];

        $recentBorrowings = Borrowing::with('member', 'book')
            ->latest()
            ->take(5)
            ->get();

        $overdueBorrowings = Borrowing::where('status', 'overdue')
            ->with('member', 'book')
            ->latest()
            ->take(5)
            ->get();

        $unpaidPenalties = Penalty::where('payment_status', 'unpaid')
            ->with('member')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentBorrowings', 'overdueBorrowings', 'unpaidPenalties'));
    }
}