<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EducationLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $programs = Program::query()
            ->latest()
            ->paginate(20)
            ->through(fn (Program $program): array => [
                'id' => $program->id,
                'title' => $program->title,
                'slug' => $program->slug,
                'division' => $program->division,
                'education_levels' => $program->education_levels->map->label()->values(),
                'opens_at' => $program->opens_at?->translatedFormat('j M Y'),
                'closes_at' => $program->closes_at?->translatedFormat('j M Y'),
                'is_published' => $program->is_published,
                'is_open' => $program->isOpen(),
            ]);

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $programs,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Programs/Form', [
            'program' => null,
            'educationLevels' => EducationLevel::options(),
        ]);
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        Program::create([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
        ]);

        return to_route('admin.programs.index')->with('status', 'Program berhasil ditambahkan.');
    }

    public function edit(Program $program): Response
    {
        return Inertia::render('Admin/Programs/Form', [
            'program' => [
                ...$program->only('id', 'title', 'slug', 'division', 'summary', 'description', 'qualifications', 'duration_months', 'quota', 'is_published'),
                'education_levels' => $program->education_levels->map->value->values(),
                'opens_at' => $program->opens_at?->toDateString(),
                'closes_at' => $program->closes_at?->toDateString(),
            ],
            'educationLevels' => EducationLevel::options(),
        ]);
    }

    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
        ]);

        return to_route('admin.programs.index')->with('status', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return to_route('admin.programs.index')->with('status', 'Program dihapus.');
    }
}
