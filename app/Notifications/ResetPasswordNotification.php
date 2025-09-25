<?php

namespace App\Notifications;

// app/Notifications/ResetPasswordNotification.php
namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('auth.reset_password_subject'))
            ->line(__('auth.reset_password_line_1'))
            ->action(__('auth.reset_password_action'), url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false)))
            ->line(__('auth.reset_password_line_2', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(__('auth.reset_password_line_3'));
    }
}
