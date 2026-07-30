<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.reset-password', [
                'actionUrl' => route('password.reset', [
                    'token' => $this->token,
                ]),
                'notifiable' => $notifiable,
            ])
            ->subject('Password Reset Request — Land Record Digitalization')
            ->greeting('Password Reset Request');
    }
}