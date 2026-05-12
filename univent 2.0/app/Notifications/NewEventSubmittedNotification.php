<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEventSubmittedNotification extends Notification
{
    use Queueable;
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Web + Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Event Baru Menunggu Persetujuan')
                    ->greeting('Halo Admin!')
                    ->line('Sebuah event baru bernama "' . $this->event->event_title . '" telah didaftarkan oleh ' . $this->event->organizer_name . '.')
                    ->line('Silakan login ke panel admin untuk meninjau event ini.')
                    ->action('Cek Event Sekarang', url('/admin/event-list'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'status' => 'pending',
            'message' => 'Event baru <b>"' . $this->event->event_title . '"</b> dari ' . $this->event->organizer_name . ' menunggu persetujuan Anda.',
        ];
    }
}