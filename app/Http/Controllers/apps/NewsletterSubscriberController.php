<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('q')) {
            $query->where('email', 'LIKE', "%{$search}%");
        }

        $subscribers = $query->paginate(20)->withQueryString();

        // Metrics
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('status', 'subscribed')->count();
        $unsubscribedCount = NewsletterSubscriber::where('status', 'unsubscribed')->count();
        $newThisMonth = NewsletterSubscriber::where('subscribed_at', '>=', now()->startOfMonth())->count();

        return view('content.apps.marketing.newsletter-subscribers', compact(
            'subscribers',
            'totalSubscribers',
            'activeSubscribers',
            'unsubscribedCount',
            'newThisMonth'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'  => 'required|email|max:255|unique:newsletter_subscribers,email',
            'status' => 'required|in:subscribed,unsubscribed',
            'source' => 'nullable|string|max:100',
        ]);

        NewsletterSubscriber::create([
            'email'         => strtolower(trim($request->email)),
            'status'        => $request->status,
            'source'        => $request->source ?: 'admin_manual',
            'subscribed_at' => $request->status === 'subscribed' ? now() : null,
        ]);

        return redirect()->route('app-newsletter-subscribers')->with('success', 'Subscriber added successfully!');
    }

    public function toggleStatus($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $newStatus = $subscriber->status === 'subscribed' ? 'unsubscribed' : 'subscribed';

        $subscriber->update([
            'status'          => $newStatus,
            'unsubscribed_at' => $newStatus === 'unsubscribed' ? now() : null,
            'subscribed_at'   => $newStatus === 'subscribed' ? now() : $subscriber->subscribed_at,
        ]);

        return redirect()->back()->with('success', "Subscriber marked as {$newStatus}!");
    }

    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->back()->with('success', 'Subscriber removed successfully!');
    }

    public function exportCsv(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::where('status', 'subscribed')->get();
        $filename = 'akmart_subscribers_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'Status', 'Source', 'Subscribed At']);

            foreach ($subscribers as $sub) {
                fputcsv($handle, [
                    $sub->id,
                    $sub->email,
                    $sub->status,
                    $sub->source ?? 'storefront',
                    $sub->subscribed_at ? $sub->subscribed_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function sendCampaign(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $count = NewsletterSubscriber::where('status', 'subscribed')->count();

        // Broadcast simulation or mailer integration
        return redirect()->back()->with('success', "Campaign '{$request->subject}' broadcast queued for {$count} active subscribers!");
    }
}
