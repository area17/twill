<?php

namespace A17\Twill\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TemporaryPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('twill::emails.html.email', [
            'url' => url(request()->getScheme() . '://' . config('twill.admin_app_url') . route('admin.login.form', null, false)),
            'actionText' => 'Login',
            'copy' => 'You are receiving this email because your password has been changed to: ' . $this->token,
        ]);
    }
}
