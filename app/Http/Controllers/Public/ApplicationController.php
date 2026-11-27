<?php

namespace App\Http\Controllers\Public;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\EducationLevel;
use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use App\Models\Program;
use App\Notifications\ApplicationSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ApplicationController extends Controller
{
    public function create(Request $request): Response
    {
        $programs = Program::query()
            ->withAcceptedCount()
            ->published()
            ->orderBy('title')
            ->get()
            ->filter(fn (Program $program): bool => $program->isAcceptingApplications())
            ->map(fn (Program $program): array => [
                'id' => $program->id,
                'title' => $program->title,
                'slug' => $program->slug,
                'division' => $program->division,
                'education_levels' => $program->education_levels->map->value->values()->all(),
                'duration_months' => $program->duration_months,
            ])
            ->values();

        $selectedProgram = $programs->firstWhere('slug', $request->query('program'));

        return Inertia::render('Public/Applications/Create', [
            'programs' => $programs,
            'selectedProgramId' => $selectedProgram['id'] ?? null,
            'educationLevels' => EducationLevel::options(),
            'genders' => array_map(fn (Gender $gender): array => ['value' => $gender->value, 'label' => $gender->label()], Gender::cases()),
        ]);
    }

    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $storedPaths = [];

        try {
            $application = DB::transaction(function () use ($request, &$storedPaths): Application {
                $application = Application::create([
                    ...$request->safe()->except(['website', 'consent', ...array_column(DocumentType::cases(), 'value')]),
                    'status' => ApplicationStatus::Submitted,
                    'consent_at' => now(),
                ]);

                foreach (DocumentType::cases() as $type) {
                    $file = $request->file($type->value);

                    if ($file === null) {
                        continue;
                    }

                    $path = $file->store("applications/{$application->code}", 'local');
                    $storedPaths[] = $path;

                    $application->documents()->create([
                        'type' => $type,
                        'path' => $path,
                        'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                        'size' => $file->getSize(),
                    ]);
                }

                $application->statusLogs()->create([
                    'to_status' => ApplicationStatus::Submitted,
                    'note' => 'Lamaran dikirim lewat website.',
                    'is_visible_to_applicant' => true,
                ]);

                return $application;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);

            throw $exception;
        }

        $application->notify(new ApplicationSubmitted($application));

        return to_route('applications.submitted')->with('submittedApplicationCode', $application->code);
    }

    public function submitted(Request $request): Response|RedirectResponse
    {
        $code = $request->session()->get('submittedApplicationCode');

        if ($code === null) {
            return to_route('applications.status');
        }

        $application = Application::query()->with('program:id,title')->where('code', $code)->firstOrFail();

        return Inertia::render('Public/Applications/Submitted', [
            'application' => [
                'code' => $application->code,
                'email' => $application->email,
                'program' => $application->program->title,
            ],
        ]);
    }
}
