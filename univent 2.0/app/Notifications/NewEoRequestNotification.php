<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEoRequestNotification extends Notification
{
    use Queueable;
    public $userRequest;

    public function __construct($userRequest)
    {
        $this->userRequest = $userRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Web + Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Pengajuan Event Organizer (EO) Baru')
                    ->greeting('Halo Admin!')
                    ->line('Pengguna bernama ' . $this->userRequest->name . ' baru saja mengajukan diri sebagai Event Organizer dengan nama instansi: ' . $this->userRequest->eo_org_name . '.')
                    ->action('Tinjau Pengajuan', url('/admin/eo-requests'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'status' => 'pending',
            'message' => '<b>' . $this->userRequest->name . '</b> mengajukan diri sebagai Event Organizer.',
        ];
    }
}