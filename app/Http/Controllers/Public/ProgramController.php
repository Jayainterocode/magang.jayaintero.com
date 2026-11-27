<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $programs = Program::query()
            ->withAcceptedCount()
            ->published()
            ->orderByDesc('opens_at')
            ->get()
            ->sortByDesc(fn (Program $program): bool => $program->isAcceptingApplications())
            ->map(fn (Program $program): array => $program->toCardArray())
            ->values();

        return Inertia::render('Public/Programs/Index', [
            'programs' => $programs,
        ]);
    }

    public function show(Program $program): Response
    {
        abort_unless($program->is_published, 404);

        return Inertia::render('Public/Programs/Show', [
            'program' => [
                ...$program->toCardArray(),
                'description_html' => Str::safeMarkdown($program->description),
                'qualifications_html' => Str::safeMarkdown($program->qualifications),
            ],
        ]);
    }
}
