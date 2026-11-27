<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateApplicationStatusRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationStatusLog;
use App\Models\Program;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only('search', 'program_id', 'status', 'education_level');

        $applications = Application::query()
            ->with('program:id,title')
            ->filter($filters)
            ->latest()
            ->paginate(25)
            ->withQueryString()
            ->through(fn (Application $application): array => [
                'id' => $application->id,
                'code' => $application->code,
                'full_name' => $application->full_name,
                'email' => $application->email,
                'program' => $application->program->title,
                'education_level' => $application->education_level->label(),
                'institution' => $application->institution,
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'submitted_at' => $application->created_at->translatedFormat('j M Y'),
            ]);

        return Inertia::render('Admin/Applications/Index', [
            'applications' => $applications,
            'filters' => $filters,
            'options' => [
                'programs' => Program::query()->orderBy('title')->get(['id', 'title']),
                'statuses' => self::statusOptions(),
                'educationLevels' => EducationLevel::options(),
            ],
        ]);
    }

    public function show(Application $application): Response
    {
        $application->load([
            'program:id,title,slug',
            'documents',
            'statusLogs' => fn ($query) => $query->with('user:id,name')->latest()->latest('id'),
        ]);

        return Inertia::render('Admin/Applications/Show', [
            'application' => [
                'id' => $application->id,
                'code' => $application->code,
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'submitted_at' => $application->created_at->translatedFormat('j F Y, H:i'),
                'program' => $application->program->only('id', 'title', 'slug'),
                'personal' => [
                    'Nama lengkap' => $application->full_name,
                    'Email' => $application->email,
                    'WhatsApp' => $application->phone,
                    'Tanggal lahir' => $application->birth_date->translatedFormat('j F Y').' ('.$application->birth_date->age.' tahun)',
                    'Jenis kelamin' => $application->gender->label(),
                    'Alamat' => $application->address,
                ],
                'education' => [
                    'Jenjang' => $application->education_level->label(),
                    'Sekolah/kampus' => $application->institution,
                    'Jurusan' => $application->major,
                    'Kelas/semester' => $application->semester,
                    'NIS/NIM' => $application->student_number,
                    'Pembimbing' => collect([$application->supervisor_name, $application->supervisor_phone])->filter()->join(' · ') ?: null,
                ],
                'internship' => [
                    'Periode' => $application->start_date->translatedFormat('j M Y').' – '.$application->end_date->translatedFormat('j M Y'),
                    'Durasi' => (int) $application->start_date->diffInMonths($application->end_date).' bulan',
                ],
                'phone_link' => self::whatsappLink($application->phone),
                'motivation' => $application->motivation,
                'documents' => $application->documents->map(fn (ApplicationDocument $document): array => [
                    'id' => $document->id,
                    'type' => $document->type->label(),
                    'original_name' => $document->original_name,
                    'size_kb' => (int) ceil($document->size / 1024),
                    'url' => route('admin.applications.documents.show', [$application, $document]),
                ]),
                'history' => $application->statusLogs->map(fn (ApplicationStatusLog $log): array => [
                    'id' => $log->id,
                    'from' => $log->from_status?->label(),
                    'to' => $log->to_status->label(),
                    'note' => $log->note,
                    'is_visible_to_applicant' => $log->is_visible_to_applicant,
                    'by' => $log->user?->name ?? 'Pelamar',
                    'date' => $log->created_at->translatedFormat('j M Y, H:i'),
                ]),
            ],
            'statuses' => self::statusOptions(),
            'emailedStatuses' => array_map(fn (ApplicationStatus $status): string => $status->value, ApplicationStatusChanged::Statuses),
        ]);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, Application $application): RedirectResponse
    {
        $newStatus = $request->enum('status', ApplicationStatus::class);
        $note = $request->validated('note');
        $isVisibleToApplicant = $request->boolean('is_visible_to_applicant');

        if ($newStatus === $application->status && blank($note)) {
            return back()->withErrors(['status' => 'Pilih status lain atau tulis catatan.']);
        }

        $previousStatus = $application->status;

        $log = DB::transaction(function () use ($application, $request, $newStatus, $previousStatus, $note, $isVisibleToApplicant): ApplicationStatusLog {
            $application->update(['status' => $newStatus]);

            return $application->statusLogs()->create([
                'user_id' => $request->user()->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'note' => $note,
                'is_visible_to_applicant' => $isVisibleToApplicant,
            ]);
        });

        $isEmailed = $newStatus !== $previousStatus && ApplicationStatusChanged::shouldNotify($newStatus);

        if ($isEmailed) {
            $application->notify(new ApplicationStatusChanged($application, $newStatus, $isVisibleToApplicant ? $log->note : null));
        }

        return back()->with('status', $isEmailed
            ? "Status diubah ke {$newStatus->label()} dan email dikirim ke pelamar."
            : 'Status lamaran diperbarui.');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private static function statusOptions(): array
    {
        return array_map(
            fn (ApplicationStatus $status): array => ['value' => $status->value, 'label' => $status->label()],
            ApplicationStatus::cases(),
        );
    }

    private static function whatsappLink(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return null;
        }

        return 'https://wa.me/'.(str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits);
    }
}
