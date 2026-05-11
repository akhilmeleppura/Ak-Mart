<?php

namespace App\Services;

use App\Models\TenantSubscription;
use App\Models\DunningLog;
use App\Mail\DunningReminderMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DunningService
{
    /**
     * Dunning sequence config:
     * Day 0 (payment fails):  Mark as past_due
     * Day 1:  Attempt 1 - Friendly reminder email
     * Day 3:  Attempt 2 - Urgent warning email
     * Day 7:  Attempt 3 - Grace period ending warning
     * Day 14: Attempt 4 - Suspend subscription
     * Day 30: Attempt 5 - Cancel subscription
     */
    protected array $sequence = [
        1  => ['type' => 'email_reminder',         'suspend' => false, 'cancel' => false],
        3  => ['type' => 'grace_period_warning',    'suspend' => false, 'cancel' => false],
        7  => ['type' => 'grace_period_warning',    'suspend' => false, 'cancel' => false],
        14 => ['type' => 'subscription_suspended',  'suspend' => true,  'cancel' => false],
        30 => ['type' => 'subscription_canceled',   'suspend' => false, 'cancel' => true],
    ];

    public function process(): void
    {
        $pastDue = TenantSubscription::where('status', 'past_due')
            ->with(['branch', 'plan'])
            ->get();

        Log::info("[Dunning] Processing " . $pastDue->count() . " past-due subscriptions.");

        foreach ($pastDue as $subscription) {
            $this->processSubscription($subscription);
        }
    }

    protected function processSubscription(TenantSubscription $subscription): void
    {
        $daysPastDue = $subscription->current_period_end
            ? (int) $subscription->current_period_end->diffInDays(now())
            : 0;

        foreach ($this->sequence as $day => $action) {
            if ($daysPastDue >= $day) {
                // Check if this attempt was already logged
                $alreadySent = DunningLog::where('tenant_subscription_id', $subscription->id)
                    ->where('type', $action['type'])
                    ->where('attempt_number', $day)
                    ->exists();

                if (!$alreadySent) {
                    $this->executeAction($subscription, $day, $action);
                }
            }
        }
    }

    protected function executeAction(TenantSubscription $subscription, int $day, array $action): void
    {
        $branch = $subscription->branch;
        $emailSent = false;

        // Send email if it's a reminder type
        if (in_array($action['type'], ['email_reminder', 'grace_period_warning'])) {
            try {
                // Get the branch owner's email
                $owner = User::where('branch_id', $subscription->branch_id)->first();
                if ($owner) {
                    Mail::to($owner->email)->send(new DunningReminderMail($subscription, $day, $action['type']));
                    $emailSent = true;
                }
            } catch (\Exception $e) {
                Log::error("[Dunning] Email failed for branch {$subscription->branch_id}: " . $e->getMessage());
            }
        }

        // Suspend subscription
        if ($action['suspend']) {
            $subscription->update(['status' => 'past_due']); // Keep as past_due with locked access
            Log::warning("[Dunning] Subscription #{$subscription->id} access suspended after {$day} days.");
        }

        // Cancel subscription
        if ($action['cancel']) {
            $subscription->update(['status' => 'canceled', 'canceled_at' => now()]);
            Log::warning("[Dunning] Subscription #{$subscription->id} CANCELED after {$day} days unpaid.");
        }

        // Log the dunning event
        DunningLog::create([
            'branch_id'              => $subscription->branch_id,
            'tenant_subscription_id' => $subscription->id,
            'attempt_number'         => $day,
            'type'                   => $action['type'],
            'email_sent'             => $emailSent,
            'sent_at'                => $emailSent ? now() : null,
            'notes'                  => "Triggered at {$day} days past due. Action: " . $action['type'],
        ]);
    }
}
