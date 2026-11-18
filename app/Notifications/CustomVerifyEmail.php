<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        if (!app()->isProduction()) {
            // Record in cache for demo sandbox testing
            cache()->put('latest_verification_' . $notifiable->getKey(), $url, now()->addHours(2));

            // Record into admin mail log for demonstration inspector
            $this->recordToMailbox($notifiable->getEmailForVerification(), 'Email Verification Notice', $url, 'verify_email');
        }

        return (new MailMessage)
            ->subject('Verify Your Email Address · BookStore')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'url' => $url,
            ]);
    }

    /**
     * Record communication to in-memory/cache log for demo inspection.
     */
    protected function recordToMailbox(string $recipient, string $subject, string $url, string $type): void
    {
        $history = cache()->get('dev_mailbox_log', []);
        array_unshift($history, [
            'id' => uniqid('mail_'),
            'type' => $type,
            'recipient' => $recipient,
            'subject' => $subject,
            'url' => $url,
            'created_at' => now()->toDateTimeString(),
        ]);
        cache()->put('dev_mailbox_log', array_slice($history, 0, 30), now()->addDays(2));
    }
}
