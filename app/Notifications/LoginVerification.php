<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginVerification extends Notification
{
    use Queueable;

    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Your Login')
            ->line('Click the button below to verify your login.')
            ->action('Verify Login', $this->url)
            ->line('If this wasn’t you, secure your account immediately.');
    }
}



