<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
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
        $isArtist = $notifiable->role && $notifiable->role->value === 'artist';

        $actionUrl = $isArtist
            ? url('/' . $notifiable->username)
            : url('/browse');

        $actionText = $isArtist ? 'Set Up Your Profile' : 'Browse Artists';

        $subject = $isArtist
            ? "Welcome to Atelier, {$notifiable->name}"
            : "Welcome to Atelier, {$notifiable->name}";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.welcome', [
                'userName' => $notifiable->name,
                'isArtist' => $isArtist,
                'actionUrl' => $actionUrl,
                'actionText' => $actionText,
                'subject' => $subject,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
