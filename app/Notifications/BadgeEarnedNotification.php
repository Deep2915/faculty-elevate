<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeEarnedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $badgeName,
        protected string $reason
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Badge Earned')
            ->line("You earned the {$this->badgeName} badge.")
            ->line("Reason: {$this->reason}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'badge_name' => $this->badgeName,
            'reason' => $this->reason,
        ];
    }
}
