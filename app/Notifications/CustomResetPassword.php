<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        if (!app()->isProduction()) {
            // Record in cache for demo sandbox testing
            cache()->put('latest_password_reset_' . md5(strtolower($notifiable->getEmailForPasswordReset())), $url, now()->addHours(2));

            // Record into admin mail log for demonstration inspector
            $this->recordToMailbox($notifiable->getEmailForPasswordReset(), 'Password Recovery Instructions', $url, 'password_reset');
        }

        return (new MailMessage)
            ->subject('Password Recovery Instructions · BookStore')
            ->view('emails.reset-password', [
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
