<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Submitted = 'submitted';
    case Screening = 'screening';
    case Interview = 'interview';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Baru',
            self::Screening => 'Seleksi berkas',
            self::Interview => 'Wawancara',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak',
        };
    }

    /**
     * What the applicant reads on the status-check page.
     */
    public function applicantDescription(): string
    {
        return match ($this) {
            self::Submitted => 'Lamaran sudah kami terima dan menunggu diperiksa.',
            self::Screening => 'Tim Jaya Intero sedang memeriksa data dan berkasmu.',
            self::Interview => 'Kamu lolos seleksi berkas. Jadwal wawancara dikirim lewat email.',
            self::Accepted => 'Selamat, kamu diterima. Info jadwal mulai magang dikirim lewat email.',
            self::Rejected => 'Mohon maaf, lamaranmu belum dapat kami terima kali ini.',
        };
    }

    /**
     * Statuses that still block a second application to the same program.
     *
     * @return list<self>
     */
    public static function active(): array
    {
        return [self::Submitted, self::Screening, self::Interview, self::Accepted];
    }
}
