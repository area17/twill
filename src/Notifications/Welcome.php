<?php

namespace A17\CmsToolkit\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class Welcome extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You are receiving this email because we an account was created for you on ' . config('app.name') . '.')
            ->action('Choose your own password', url(config('cms-toolkit.admin_app_url') . route('admin.password.reset.welcome.form', $this->token, false)));

    }
}
