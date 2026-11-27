<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckApplicationStatusRequest;
use App\Models\Application;
use App\Models\ApplicationStatusLog;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationStatusController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Public/Applications/Status', [
            'result' => null,
        ]);
    }

    /**
     * Looks an application up by code + email. A wrong pair gets the same
     * generic answer as a missing code, so codes cannot be probed by email.
     */
    public function check(CheckApplicationStatusRequest $request): Response
    {
        $application = Application::query()
            ->with([
                'program:id,title',
                'statusLogs' => fn ($query) => $query->where('is_visible_to_applicant', true)->oldest(),
            ])
            ->where('code', $request->validated('code'))
            ->where('email', $request->validated('email'))
            ->first();

        if ($application === null) {
            return Inertia::render('Public/Applications/Status', [
                'result' => null,
                'notFound' => true,
                'lookup' => $request->only('code', 'email'),
            ]);
        }

        return Inertia::render('Public/Applications/Status', [
            'lookup' => $request->only('code', 'email'),
            'result' => [
                'code' => $application->code,
                'full_name' => $application->full_name,
                'program' => $application->program->title,
                'submitted_at' => $application->created_at->translatedFormat('j F Y'),
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'status_description' => $application->status->applicantDescription(),
                'timeline' => $application->statusLogs->map(fn (ApplicationStatusLog $log): array => [
                    'id' => $log->id,
                    'status_label' => $log->to_status->label(),
                    'note' => $log->note,
                    'date' => $log->created_at->translatedFormat('j F Y, H:i'),
                ]),
            ],
        ]);
    }
}
