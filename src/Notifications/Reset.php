<?php

namespace A17\Twill\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

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
            'actionText' => Lang::getFromJson('Reset Password Notification'),
            'copy' => Lang::getFromJson('You are receiving this email because we received a password reset request for your account.') .
                ' ' .
                Lang::getFromJson('If you did not request a password reset, no further action is required.')
        ]);
    }
}
