<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EoRequestStatusNotification extends Notification
{
    use Queueable;
    public $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Web + Email
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusText = $this->status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
        
        $mail = (new MailMessage)
                    ->subject('Status Pengajuan Event Organizer Anda')
                    ->greeting('Halo!')
                    ->line('Pengajuan Anda untuk menjadi Event Organizer (EO) di Univent telah ' . $statusText . ' oleh Admin.');

        if ($this->status === 'approved') {
            $mail->line('Selamat! Sekarang Anda dapat mulai membuat dan mendaftarkan event di platform kami.')
                 ->action('Buat Event Pertama Anda', url('/submit-event'));
        } else {
            $mail->line('Mohon maaf, pengajuan Anda belum dapat kami terima saat ini. Silakan periksa kembali data Anda atau hubungi Admin untuk informasi lebih lanjut.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $statusText = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        return [
            'status' => $this->status,
            'message' => 'Pengajuan Event Organizer Anda telah <b>' . $statusText . '</b> oleh Admin.',
        ];
    }
}