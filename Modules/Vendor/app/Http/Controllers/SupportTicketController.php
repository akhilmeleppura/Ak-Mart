<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of tickets for the current branch.
     */
    public function index()
    {
        $tickets = SupportTicket::with(['user'])
            ->latest()
            ->paginate(15);

        return view('content.apps.vendor.support-tickets', compact('tickets'));
    }

    /**
     * Display the specified ticket conversation.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'messages.user']);
        return view('content.apps.vendor.support-ticket-show', compact('ticket'));
    }

    /**
     * Store a new reply to the ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        // If vendor replies, update status to in_progress
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Update the ticket status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $ticket->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Ticket status updated.');
    }
}
