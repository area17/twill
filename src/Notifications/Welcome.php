<?php

namespace A17\Twill\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class Welcome extends ResetPassword
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
            'url' => url(request()->getScheme() . '://' . config('twill.admin_app_url') . route('admin.password.reset.welcome.form', $this->token, false)),
            'actionText' => 'Choose your own password',
            'title' => 'Welcome',
            'copy' => 'You are receiving this email because an account was created for you on ' . config('app.name') . '.',
        ]);

    }
}
