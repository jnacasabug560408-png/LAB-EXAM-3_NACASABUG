<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::paginate(15);
        $stats = [
            'total' => Member::count(),
            'active' => Member::where('status', 'active')->count(),
            'inactive' => Member::where('status', 'inactive')->count(),
            'suspended' => Member::where('status', 'suspended')->count(),
        ];
        return view('members.index', compact('members', 'stats'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'membership_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['member_id'] = 'MEM-' . strtoupper(Str::random(8));

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member added successfully!');
    }

    public function show(Member $member)
    {
        $borrowings = $member->borrowings()->with('book')->latest()->paginate(10);
        $penalties = $member->penalties()->latest()->paginate(10);
        return view('members.show', compact('member', 'borrowings', 'penalties'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'membership_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully!');
    }

    public function destroy(Member $member)
    {
        if ($member->borrowings()->exists()) {
            return redirect()->route('members.index')
                ->with('error', 'Cannot delete member with borrowing records.');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully!');
    }

    public function borrowingHistory(Member $member)
    {
        $borrowings = $member->borrowings()->with('book')->latest()->paginate(20);
        return view('members.borrowing-history', compact('member', 'borrowings'));
    }
}