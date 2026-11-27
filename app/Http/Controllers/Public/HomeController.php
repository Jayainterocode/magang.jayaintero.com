<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Program;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $page = Page::forKey('home');

        $openPrograms = Program::query()
            ->published()
            ->orderByDesc('opens_at')
            ->get()
            ->filter(fn (Program $program): bool => $program->isAcceptingApplications())
            ->take(3)
            ->map(fn (Program $program): array => $program->toCardArray())
            ->values();

        $latestPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Post $post): array => PostController::card($post));

        return Inertia::render('Public/Home', [
            'hero' => [
                'title' => $page->title,
                'intro' => $page->intro,
            ],
            'aboutHtml' => Str::safeMarkdown($page->body),
            'programs' => $openPrograms,
            'faqs' => FaqController::published(limit: 4),
            'gallery' => GalleryController::items(limit: 6),
            'posts' => $latestPosts,
        ]);
    }
}
