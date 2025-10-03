<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class VerifyEmailWithPassword extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    protected function buildMailMessage($url = null)
    {
        return (new MailMessage)
            ->subject(Lang::get('Account Created - ').config('app.name'))
            ->line(Lang::get('Your account has been created successfully!'))
            ->action(Lang::get('Log In Now'), route('login'))
            ->line(Lang::get('If you did not create an account, no further action is required.'));
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Created - '.config('app.name'))
            ->line('Your account has been created successfully!')
            ->line('Here are your login credentials:')
            ->line('**Email:** '.$notifiable->email)
            ->line('**Password:** '.$this->password)
            ->line('Please click the button below to setup your account.')
            ->action('Log in now', route('login'))
            ->line('After verifying, we recommend changing your password on first login.')
            ->line('If you did not create an account, no further action is required.');
    }
}
