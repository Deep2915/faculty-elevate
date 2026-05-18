<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BurnoutAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $facultyName,
        private readonly float $currentBurnoutIndex
    ) {}

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
        $score = number_format($this->currentBurnoutIndex, 1);

        return (new MailMessage)
            ->subject('Burnout Alert: Faculty Support Needed')
            ->line("{$this->facultyName} has reported low wellbeing in consecutive surveys.")
            ->line("Current burnout index: {$score}")
            ->line('Please review wellbeing trends and provide support as needed.')
            ->action('Open HOD Dashboard', url('/hod/dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'faculty_name' => $this->facultyName,
            'burnout_index' => $this->currentBurnoutIndex,
        ];
    }
}
