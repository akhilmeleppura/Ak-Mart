<?php

namespace App\Mail;

use App\Models\TenantSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DunningReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public TenantSubscription $subscription;
    public int $daysPastDue;
    public string $type;

    public function __construct(TenantSubscription $subscription, int $daysPastDue, string $type)
    {
        $this->subscription = $subscription;
        $this->daysPastDue  = $daysPastDue;
        $this->type         = $type;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'email_reminder'      => '⚠️ Action Required: Update Your Payment Method',
            'grace_period_warning' => '🚨 Urgent: Your Subscription Access Ending Soon',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Important Notice About Your Subscription',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dunning-reminder',
            with: [
                'subscription' => $this->subscription,
                'daysPastDue'  => $this->daysPastDue,
                'type'         => $this->type,
                'planName'     => $this->subscription->plan->name ?? 'Your Plan',
                'billingUrl'   => url('/app/saas/billing'),
            ]
        );
    }
}
