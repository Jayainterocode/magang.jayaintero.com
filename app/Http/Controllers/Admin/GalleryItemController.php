<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryItemRequest;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GalleryItemController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Gallery/Index', [
            'items' => GalleryItem::query()->ordered()->get()->map(fn (GalleryItem $item): array => [
                'id' => $item->id,
                'caption' => $item->caption,
                'sort_order' => $item->sort_order,
                'image_url' => $item->imageUrl(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Gallery/Form', [
            'item' => null,
        ]);
    }

    public function store(GalleryItemRequest $request): RedirectResponse
    {
        GalleryItem::create([
            ...$request->safe()->only('caption', 'sort_order'),
            'image' => $request->file('image')->store('gallery', 'public'),
        ]);

        return to_route('admin.gallery.index')->with('status', 'Foto berhasil ditambahkan.');
    }

    public function edit(GalleryItem $gallery): Response
    {
        return Inertia::render('Admin/Gallery/Form', [
            'item' => [
                ...$gallery->only('id', 'caption', 'sort_order'),
                'image_url' => $gallery->imageUrl(),
            ],
        ]);
    }

    public function update(GalleryItemRequest $request, GalleryItem $gallery): RedirectResponse
    {
        $attributes = $request->safe()->only('caption', 'sort_order');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image);
            $attributes['image'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($attributes);

        return to_route('admin.gallery.index')->with('status', 'Foto berhasil diperbarui.');
    }

    public function destroy(GalleryItem $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();

        return to_route('admin.gallery.index')->with('status', 'Foto dihapus.');
    }
}
