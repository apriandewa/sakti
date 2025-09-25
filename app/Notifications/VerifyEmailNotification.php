<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseNotification
{
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('auth.verify_email_subject'))
            ->line(__('auth.verify_email_line_1'))
            ->action(__('auth.verify_email_action'), $this->verificationUrl($notifiable))
            ->line(__('auth.verify_email_line_2'));
    }
}
