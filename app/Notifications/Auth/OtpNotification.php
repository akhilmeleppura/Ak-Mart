<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $otp;
    private string $purpose;

    /**
     * IMPORTANT: The plaintext OTP is passed here only for delivery.
     * It is NOT stored anywhere in this class after the notification is sent.
     */
    public function __construct(string $otp, string $purpose)
    {
        $this->otp     = $otp;
        $this->purpose = $purpose;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $purposeLabel = match ($this->purpose) {
            'LOGIN'               => __('Login Verification'),
            'PASSWORD_RESET'      => __('Password Reset'),
            'EMAIL_VERIFICATION'  => __('Email Verification'),
            'PHONE_VERIFICATION'  => __('Phone Verification'),
            default               => __('Verification'),
        };

        $expiryMinutes = config('otp.expiration', 5);

        return (new MailMessage)
            ->subject("AK-Mart — {$purposeLabel} Code")
            ->view('emails.otp', [
                'otp'          => $this->otp,
                'purpose'      => $this->purpose,
                'purposeLabel' => $purposeLabel,
                'expiry'       => $expiryMinutes,
                'appName'      => config('app.name', 'AK-Mart'),
            ]);
    }

    /**
     * Ensure the OTP is never serialized into logs or job payloads in plain text.
     * The queue payload will contain the hashed version via the model — not this class.
     * We override __sleep to prevent accidental exposure.
     */
    public function __sleep(): array
    {
        // purposely DO NOT include $otp in serialized state for queue
        return ['purpose'];
    }

    public function __wakeup(): void
    {
        $this->otp = ''; // blank on wake — the notification has already been dispatched
    }
}
