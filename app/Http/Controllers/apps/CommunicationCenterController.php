<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\CommunicationTemplate;
use App\Models\MarketingCampaign;
use App\Models\User;
use App\Services\CommunicationService;
use Illuminate\Http\Request;

class CommunicationCenterController extends Controller
{
    protected CommunicationService $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Unified Communication Hub Dashboard.
     */
    public function index()
    {
        CommunicationLog::ensureTableExists();
        CommunicationTemplate::ensureTableExists();
        MarketingCampaign::ensureTableExists();

        $stats = [
            'total_emails'    => CommunicationLog::where('channel', 'email')->count(),
            'total_whatsapp'  => CommunicationLog::where('channel', 'whatsapp')->count(),
            'total_delivered' => CommunicationLog::whereIn('status', ['sent', 'delivered'])->count(),
            'total_failed'    => CommunicationLog::where('status', 'failed')->count(),
        ];

        $recentLogs = CommunicationLog::latest()->take(20)->get();
        $templates = CommunicationTemplate::all();
        $campaigns = MarketingCampaign::latest()->take(10)->get();

        return view('content.apps.marketing.communication-center', compact('stats', 'recentLogs', 'templates', 'campaigns'));
    }

    /**
     * Send individual or test message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'channel'       => 'required|in:email,whatsapp,in_app',
            'recipient'     => 'required|string',
            'template_code' => 'required|string',
            'variables'     => 'nullable|array',
        ]);

        $log = $this->communicationService->send(
            $request->channel,
            $request->recipient,
            $request->template_code,
            $request->variables ?? ['message' => $request->custom_message ?? 'Notification update']
        );

        if ($log->status === 'failed') {
            return redirect()->back()->with('error', "Communication failed: {$log->error_message}");
        }

        return redirect()->back()->with('success', "Message dispatched via " . ucfirst($request->channel) . " successfully!");
    }

    /**
     * Store or update a template.
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'code'    => 'required|string|max:100',
            'name'    => 'required|string|max:255',
            'channel' => 'required|in:email,whatsapp,sms',
            'body'    => 'required|string',
        ]);

        CommunicationTemplate::ensureTableExists();
        CommunicationTemplate::updateOrCreate(
            ['code' => $request->code, 'channel' => $request->channel],
            [
                'name'     => $request->name,
                'subject'  => $request->subject ?? $request->name,
                'category' => $request->category ?? 'transactional',
                'body'     => $request->body,
                'is_active'=> $request->boolean('is_active', true),
            ]
        );

        return redirect()->back()->with('success', 'Communication template saved successfully!');
    }

    /**
     * Dispatch or schedule a marketing campaign.
     */
    public function launchCampaign(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'channel'         => 'required|in:email,whatsapp,omnichannel',
            'audience_type'   => 'required|string',
            'message_content' => 'required|string',
        ]);

        MarketingCampaign::ensureTableExists();

        // Count audience
        $recipientsQuery = User::whereNotNull('email');
        if ($request->audience_type === 'vip') {
            $recipientsQuery->where('orders_count', '>=', 5);
        }

        $recipientCount = $recipientsQuery->count();

        $campaign = MarketingCampaign::create([
            'name'             => $request->name,
            'channel'          => $request->channel,
            'audience_type'    => $request->audience_type,
            'subject'          => $request->subject ?? $request->name,
            'message_content'  => $request->message_content,
            'status'           => 'running',
            'recipients_count' => $recipientCount,
            'sent_count'       => $recipientCount,
            'delivered_count'  => $recipientCount,
        ]);

        return redirect()->back()->with('success', "Campaign '{$campaign->name}' queued for {$recipientCount} recipients!");
    }
}
