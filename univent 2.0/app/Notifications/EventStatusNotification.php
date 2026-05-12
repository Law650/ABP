<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventStatusNotification extends Notification
{
    use Queueable;

    public $event;
    public $status;

    /**
     * Menerima data event dan status (approved/rejected) dari Controller
     */
    public function __construct($event, $status)
    {
        $this->event = $event;
        $this->status = $status;
    }

    /**
     * Tentukan pengiriman via apa (kita pakai email/mail)
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Desain dan isi dari Email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->status === 'approved' 
                   ? '🎉 Selamat! Event Anda Telah Disetujui' 
                   : '❌ Mohon Maaf, Event Anda Ditolak';

        $mail = (new MailMessage)
                    ->subject($subject)
                    ->greeting('Halo, ' . $notifiable->name . '!')
                    ->line('Status pengajuan event Anda yang berjudul **"' . $this->event->event_title . '"** telah diperbarui oleh Admin.')
                    ->line('Status saat ini: **' . strtoupper($this->status) . '**');

        if ($this->status === 'approved') {
            $mail->line('Event Anda sekarang sudah tayang dan bisa dilihat oleh seluruh mahasiswa/i Univent.')
                 ->action('Lihat Event', url('/event-detail/' . $this->event->id)); // Sesuaikan url jika beda
        } else {
            $mail->line('Sayang sekali, event Anda belum dapat ditayangkan untuk saat ini. Silakan hubungi Admin atau balas email ini untuk informasi lebih lanjut mengenai alasan penolakan.');
        }

        return $mail->line('Terima kasih telah menggunakan Univent!');
    }

    public function toArray(object $notifiable): array
    {
        $statusText = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        
        return [
            'status' => $this->status,
            'message' => 'Event Anda <b>"' . $this->event->event_title . '"</b> telah <b>' . $statusText . '</b> oleh Admin.',
        ];
    }
}