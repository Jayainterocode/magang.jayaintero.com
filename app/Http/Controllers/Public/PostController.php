<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->paginate(9)
            ->through(fn (Post $post): array => $this->card($post));

        return Inertia::render('Public/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->isPublished(), 404);

        return Inertia::render('Public/Posts/Show', [
            'post' => [
                ...$this->card($post),
                'body_html' => Str::safeMarkdown($post->body),
            ],
        ]);
    }

    /**
     * @return array{title: string, slug: string, category: string, excerpt: string, cover_image_url: ?string, published_at: ?string}
     */
    public static function card(Post $post): array
    {
        return [
            'title' => $post->title,
            'slug' => $post->slug,
            'category' => $post->category,
            'excerpt' => $post->excerpt,
            'cover_image_url' => $post->coverImageUrl(),
            'published_at' => $post->published_at?->translatedFormat('j F Y'),
        ];
    }
}
