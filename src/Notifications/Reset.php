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
        return (new MailMessage())
            ->subject(twillTrans('twill::lang.notifications.reset.subject', [
                'appName' => config('app.name')
            ]))
            ->markdown('twill::emails.html.email', [
                'url' => route('twill.password.reset.form', $this->token),
                'actionText' => twillTrans('twill::lang.notifications.reset.action'),
                'copy' => twillTrans('twill::lang.notifications.reset.content'),
            ]);
    }
}
