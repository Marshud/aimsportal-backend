<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class UserPasswordResetEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = $this->passwordResetUrl($notifiable);
        return (new MailMessage)
                    ->subject('Password Reset')
                    ->line('Please click the button below to reset your password.')
                    ->action('Reset', $resetUrl)
                    ->line('If you did not initiate this process, no further action is required');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    private function passwordResetUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'users.reset.password',
            Carbon::now()->addMinutes(Config::get('auth.passwords.users.expire',60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->email),
                'user' => $notifiable
            ]
        );
    }
}
