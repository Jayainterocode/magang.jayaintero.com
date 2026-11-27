<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Post;
use App\Models\Program;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $publishedPrograms = Program::query()->published()->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'open_programs' => $publishedPrograms->filter->isAcceptingApplications()->count(),
                'published_programs' => $publishedPrograms->count(),
                'published_posts' => Post::query()->published()->count(),
                'faqs' => Faq::query()->published()->count(),
                'gallery_items' => GalleryItem::query()->count(),
            ],
        ]);
    }
}
