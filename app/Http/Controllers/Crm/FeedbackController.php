<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Feedback;
use App\Models\Guest;
use App\Models\Reservation;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();

        $feedback = Feedback::query()
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->integer('rating')))
            ->with('guest', 'branch')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => Feedback::count(),
            'unresolved' => Feedback::where('status', 'unresolved')->count(),
            'resolved' => Feedback::where('status', 'resolved')->count(),
        ];

        return view('crm.feedback.index', compact('feedback', 'counts', 'status'));
    }

    public function create()
    {
        return view('crm.feedback.create', [
            'guests' => Guest::orderBy('first_name')->get(),
            'reservations' => Reservation::with('guest')->latest()->take(50)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Feedback::create($this->validated($request));

        return redirect()->route('feedback.index')->with('success', 'Guest feedback recorded.');
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('guest', 'reservation');

        return view('crm.feedback.show', compact('feedback'));
    }

    public function resolve(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'resolution_notes' => 'nullable|string',
        ]);

        $feedback->update([
            'status' => 'resolved',
            'resolution_notes' => $validated['resolution_notes'] ?? $feedback->resolution_notes,
        ]);

        return back()->with('success', 'Feedback marked as resolved.');
    }

    public function escalate(Request $request, Feedback $feedback)
    {
        $action = Action::create([
            'branch_id' => $feedback->branch_id,
            'guest_id' => $feedback->guest_id,
            'feedback_id' => $feedback->id,
            'created_by' => $request->user()->id,
            'title' => 'Follow up on feedback #'.$feedback->id,
            'description' => $feedback->comment,
            'type' => 'feedback-follow-up',
            'priority' => $feedback->rating <= 2 ? 'high' : 'medium',
            'status' => 'Open',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        return redirect()->route('actions.index')
            ->with('success', "Follow-up action #{$action->id} created for this feedback.");
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'rating' => 'required|integer|min:1|max:5',
            'category' => 'required|string|max:100',
            'comment' => 'required|string',
            'status' => 'required|in:unresolved,resolved',
        ]);
    }
}
