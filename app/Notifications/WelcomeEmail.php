<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isArtist = $notifiable->role === 'artist';

        return (new MailMessage)
            ->subject("Welcome to Atelier, {$notifiable->name}")
            ->greeting("Hey {$notifiable->name}!")
            ->line($isArtist
                ? "Your artist account is ready. You can now set up your profile and start receiving commission requests."
                : "You're all set! Browse artists, follow your favorites, and send your first commission request.")
            ->action($isArtist ? 'Set Up Your Profile' : 'Browse Artists', url('/'))
            ->line($isArtist
                ? "Tip: Add modules to your page to showcase your work, set your commission status, and build your presence."
                : "Tip: Follow artists you like to get updates when they post new work or open their commission queue.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
