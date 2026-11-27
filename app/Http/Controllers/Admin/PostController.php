<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->with('author:id,name')
            ->latest()
            ->paginate(20)
            ->through(fn (Post $post): array => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'category' => $post->category,
                'author' => $post->author?->name,
                'is_published' => $post->isPublished(),
                'published_at' => $post->published_at?->translatedFormat('j M Y'),
            ]);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Form', [
            'post' => null,
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        Post::create([
            ...$request->safe()->only('title', 'category', 'excerpt', 'body'),
            'user_id' => $request->user()->id,
            'cover_image' => $request->file('cover_image')?->store('posts', 'public'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        return to_route('admin.posts.index')->with('status', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Posts/Form', [
            'post' => [
                ...$post->only('id', 'title', 'slug', 'category', 'excerpt', 'body'),
                'cover_image_url' => $post->coverImageUrl(),
                'is_published' => $post->isPublished(),
            ],
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $attributes = $request->safe()->only('title', 'category', 'excerpt', 'body');

        if ($request->hasFile('cover_image') || $request->boolean('remove_cover_image')) {
            $this->deleteCoverImage($post);
            $attributes['cover_image'] = $request->file('cover_image')?->store('posts', 'public');
        }

        $attributes['published_at'] = match (true) {
            ! $request->boolean('is_published') => null,
            $post->isPublished() => $post->published_at,
            default => now(),
        };

        $post->update($attributes);

        return to_route('admin.posts.index')->with('status', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteCoverImage($post);
        $post->delete();

        return to_route('admin.posts.index')->with('status', 'Artikel dihapus.');
    }

    private function deleteCoverImage(Post $post): void
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }
    }
}
