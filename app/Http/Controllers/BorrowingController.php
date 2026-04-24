<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\Member;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with('member', 'book')
            ->latest()
            ->paginate(15);
        
        // Update status for overdue items
        foreach ($borrowings as $borrowing) {
            $borrowing->updateStatus();
        }

        $stats = [
            'total' => Borrowing::count(),
            'borrowed' => Borrowing::where('status', 'borrowed')->count(),
            'returned' => Borrowing::where('status', 'returned')->count(),
            'overdue' => Borrowing::where('status', 'overdue')->count(),
        ];

        return view('borrowings.index', compact('borrowings', 'stats'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->get();
        $books = Book::where('available_copies', '>', 0)->get();
        return view('borrowings.create', compact('members', 'books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
            'quantity' => 'required|integer|min:1',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $book = Book::findOrFail($validated['book_id']);

        // Check member eligibility
        if (!$member->canBorrow()) {
            return redirect()->back()
                ->with('error', 'Member cannot borrow at this time (pending penalties or inactive status).');
        }

        // Check book availability
        if ($book->available_copies < $validated['quantity']) {
            return redirect()->back()
                ->with('error', 'Not enough copies available.');
        }

        // Create borrowing record
        $borrowing = Borrowing::create($validated);

        // Decrease available copies
        $book->decreaseAvailableCopies($validated['quantity']);

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Borrowing record created successfully!');
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->updateStatus();
        return view('borrowings.show', compact('borrowing'));
    }

    public function edit(Borrowing $borrowing)
    {
        $members = Member::where('status', 'active')->get();
        $books = Book::get();
        return view('borrowings.edit', compact('borrowing', 'members', 'books'));
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after:borrow_date',
        ]);

        $borrowing->update($validated);

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Borrowing record updated successfully!');
    }

    public function return(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'return_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        $daysOverdue = $borrowing->getDaysOverdue();

        // Mark as returned
        $borrowing->markAsReturned($validated['return_date']);

        // Create penalty if overdue
        if ($daysOverdue > 0) {
            $penaltyAmount = $daysOverdue * 50; // 50 per day
            
            Penalty::create([
                'member_id' => $borrowing->member_id,
                'borrowing_id' => $borrowing->id,
                'days_overdue' => $daysOverdue,
                'penalty_amount' => $penaltyAmount,
                'penalty_date' => now(),
            ]);
        }

        return redirect()->route('borrowings.show', $borrowing)
            ->with('success', 'Book returned successfully!' . ($daysOverdue > 0 ? ' Penalty generated.' : ''));
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return redirect()->route('borrowings.index')
                ->with('error', 'Cannot delete returned or overdue records.');
        }

        // Restore book copies
        $borrowing->book->increaseAvailableCopies($borrowing->quantity);
        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', 'Borrowing record deleted successfully!');
    }

    public function report()
    {
        $borrowings = Borrowing::with('member', 'book')->latest()->get();
        
        foreach ($borrowings as $borrowing) {
            $borrowing->updateStatus();
        }

        return view('borrowings.report', compact('borrowings'));
    }
}