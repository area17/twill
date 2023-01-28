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
        return (new MailMessage())
            ->subject(twillTrans('twill::lang.notifications.welcome.subject', [
                'appName' => config('app.name')
            ]))
            ->markdown('twill::emails.html.email', [
                'url' => route('twill.password.reset.welcome.form', $this->token),
                'actionText' => twillTrans('twill::lang.notifications.welcome.action'),
                'title' => twillTrans('twill::lang.notifications.welcome.title'),
                'copy' => twillTrans('twill::lang.notifications.welcome.content', ['name' => config('app.name')]),
            ]);
    }
}
