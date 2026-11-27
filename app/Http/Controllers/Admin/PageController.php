<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        $pages = collect(Page::Keys)->map(function (string $label, string $key): array {
            $page = Page::forKey($key);

            return [
                'key' => $key,
                'label' => $label,
                'title' => $page->title,
                'updated_at' => $page->updated_at?->translatedFormat('j M Y, H:i'),
            ];
        })->values();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
        ]);
    }

    public function edit(string $key): Response
    {
        abort_unless(array_key_exists($key, Page::Keys), 404);

        return Inertia::render('Admin/Pages/Form', [
            'page' => [
                ...Page::forKey($key)->only('key', 'title', 'intro', 'body'),
                'label' => Page::Keys[$key],
            ],
        ]);
    }

    public function update(PageRequest $request, string $key): RedirectResponse
    {
        abort_unless(array_key_exists($key, Page::Keys), 404);

        Page::query()->updateOrCreate(['key' => $key], $request->validated());

        return to_route('admin.pages.index')->with('status', 'Halaman berhasil diperbarui.');
    }
}
