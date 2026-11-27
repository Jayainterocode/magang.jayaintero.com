<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationExportController extends Controller
{
    private const Columns = [
        'Kode', 'Tanggal daftar', 'Status', 'Program', 'Nama lengkap', 'Email', 'WhatsApp',
        'Tanggal lahir', 'Jenis kelamin', 'Alamat', 'Jenjang', 'Sekolah/kampus', 'Jurusan',
        'Kelas/semester', 'NIS/NIM', 'Pembimbing', 'Kontak pembimbing', 'Mulai', 'Selesai',
    ];

    /**
     * Downloads the filtered application list as CSV. A UTF-8 BOM and the
     * semicolon separator make it open correctly in Excel with an Indonesian locale.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $filters = $request->only('search', 'program_id', 'status', 'education_level');
        $filename = 'lamaran-magang-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, self::Columns, ';', escape: '');

            Application::query()
                ->with('program:id,title')
                ->filter($filters)
                ->oldest()
                ->lazy()
                ->each(function (Application $application) use ($output): void {
                    fputcsv($output, array_map(self::guardAgainstFormulas(...), [
                        $application->code,
                        $application->created_at->format('Y-m-d H:i'),
                        $application->status->label(),
                        $application->program->title,
                        $application->full_name,
                        $application->email,
                        $application->phone,
                        $application->birth_date->format('Y-m-d'),
                        $application->gender->label(),
                        $application->address,
                        $application->education_level->label(),
                        $application->institution,
                        $application->major,
                        $application->semester,
                        $application->student_number,
                        $application->supervisor_name,
                        $application->supervisor_phone,
                        $application->start_date->format('Y-m-d'),
                        $application->end_date->format('Y-m-d'),
                    ]), ';', escape: '');
                });

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Applicant-typed text starting with =, +, - or @ would run as a formula in Excel.
     */
    private static function guardAgainstFormulas(?string $value): ?string
    {
        if ($value !== null && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
