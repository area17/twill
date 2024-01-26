<?php

namespace A17\Twill\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TemporaryPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage())->markdown('twill::emails.html.email', [
            'url' => route(config('twill.admin_route_name_prefix') . 'login.form'),
            'actionText' => 'Login',
            'copy' => 'You are receiving this email because your password has been changed to: ' . $this->token,
        ]);
    }
}
