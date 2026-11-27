<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Post;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $publishedPrograms = Program::query()->published()->withAcceptedCount()->get();

        return Inertia::render('Admin/Dashboard', [
            'contentStats' => [
                'open_programs' => $publishedPrograms->filter->isAcceptingApplications()->count(),
                'published_programs' => $publishedPrograms->count(),
                'published_posts' => Post::query()->published()->count(),
                'faqs' => Faq::query()->published()->count(),
                'gallery_items' => GalleryItem::query()->count(),
            ],
            'applicationStats' => $request->user()->can('manage-recruitment') ? $this->applicationStats() : null,
        ]);
    }

    /**
     * Applicant data is only built for admins; editors never receive it.
     *
     * @return array{total: int, this_week: int, by_status: list<array{status: string, label: string, count: int}>, by_program: list<array{title: string, count: int}>, latest: list<array<string, mixed>>}
     */
    private function applicationStats(): array
    {
        $countsByStatus = Application::query()
            ->toBase()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'total' => (int) $countsByStatus->sum(),
            'this_week' => Application::query()->where('created_at', '>=', now()->startOfWeek())->count(),
            'by_status' => array_map(fn (ApplicationStatus $status): array => [
                'status' => $status->value,
                'label' => $status->label(),
                'count' => (int) ($countsByStatus[$status->value] ?? 0),
            ], ApplicationStatus::cases()),
            'by_program' => Program::query()
                ->whereHas('applications')
                ->withCount('applications')
                ->orderByDesc('applications_count')
                ->limit(6)
                ->get()
                ->map(fn (Program $program): array => ['title' => $program->title, 'count' => $program->applications_count])
                ->all(),
            'latest' => Application::query()
                ->with('program:id,title')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (Application $application): array => [
                    'id' => $application->id,
                    'code' => $application->code,
                    'full_name' => $application->full_name,
                    'program' => $application->program->title,
                    'status' => $application->status->value,
                    'status_label' => $application->status->label(),
                    'submitted_at' => $application->created_at->diffForHumans(),
                ])
                ->all(),
        ];
    }
}
