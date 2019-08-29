<?php

namespace A17\Twill\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class Reset extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('twill::emails.html.email', [
            'url' => url(request()->getScheme() . '://' . config('twill.admin_app_url') . route('admin.password.reset.form', $this->token, false)),
            'actionText' => 'Reset password',
            'copy' => 'You are receiving this email because we received a password reset. If you did not request a password reset, no further action is required.',
        ]);
    }
}
