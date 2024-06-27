<?php

namespace App\Notifications;

use App\Helpers\LaravelMail;
use App\Models\Person;
use App\Models\Translation;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class ResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Prepares link with Token, render email and send it to person
     */
    public function toMail($notifiable)
    {
        $renderedEmail = view('email_templates/password_reset_code', [
            'url' => env('FRONTEND_URL') . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset()),
            'personName' => $notifiable->getPersonName()
        ]);
        $mailer = new LaravelMail($notifiable->getEmailForPasswordReset(), ucfirst(translate('your password reset link')), $renderedEmail);
        return $mailer->sendMail();
    }
}