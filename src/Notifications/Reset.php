<?php

namespace A17\CmsToolkit\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class Reset extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset password', url('http://' . config('cms-toolkit.admin_app_url') . route('admin.password.reset.form', $this->token, false)))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
