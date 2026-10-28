<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $clinicName, public string $invitationUrl) {}

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
        return (new MailMessage)
            ->subject('You are invited to join '.$this->clinicName.' on CareOn')
            ->greeting('You have a clinic invitation')
            ->line('You have been invited to join '.$this->clinicName.' as a doctor on CareOn.')
            ->action('Review invitation', $this->invitationUrl)
            ->line('This one-time invitation expires in 72 hours.');
    }
}
