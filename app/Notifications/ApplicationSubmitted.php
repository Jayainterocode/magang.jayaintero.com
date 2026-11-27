<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Lamaran magang diterima · {$this->application->code}")
            ->greeting("Halo {$this->application->full_name},")
            ->line("Terima kasih sudah melamar program **{$this->application->program->title}** di Jaya Intero.")
            ->line("Kode lamaranmu: **{$this->application->code}**")
            ->line('Simpan kode ini. Kamu bisa memantau status lamaran dengan kode tersebut dan email ini.')
            ->action('Cek status lamaran', route('applications.status'))
            ->line('Tim kami akan memeriksa data dan berkasmu. Kabar berikutnya dikirim lewat email ini.')
            ->salutation('Salam, Tim Magang Jaya Intero');
    }
}
