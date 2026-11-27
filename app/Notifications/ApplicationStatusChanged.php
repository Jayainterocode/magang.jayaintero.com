<?php

namespace App\Notifications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when an application reaches a status the applicant must act on or know about.
 */
class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public const Statuses = [
        ApplicationStatus::Interview,
        ApplicationStatus::Accepted,
        ApplicationStatus::Rejected,
    ];

    public function __construct(
        public Application $application,
        public ApplicationStatus $status,
        public ?string $note = null,
    ) {}

    public static function shouldNotify(ApplicationStatus $status): bool
    {
        return in_array($status, self::Statuses, true);
    }

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
        $status = $this->status;

        $mail = (new MailMessage)
            ->subject("Kabar lamaran magang: {$status->label()} · {$this->application->code}")
            ->greeting("Halo {$this->application->full_name},")
            ->line("Ada kabar terbaru untuk lamaranmu di program **{$this->application->program->title}** ({$this->application->code}).")
            ->line("**Status: {$status->label()}**")
            ->line($status->applicantDescription());

        if (filled($this->note)) {
            $mail->line('Catatan dari tim kami:')->line($this->note);
        }

        return $mail
            ->action('Cek status lamaran', route('applications.status'))
            ->salutation('Salam, Tim Magang Jaya Intero');
    }
}
