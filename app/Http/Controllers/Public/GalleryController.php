<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Gallery', [
            'items' => self::items(),
        ]);
    }

    /**
     * @return list<array{id: int, caption: string, image_url: string}>
     */
    public static function items(?int $limit = null): array
    {
        return GalleryItem::query()
            ->ordered()
            ->when($limit, fn ($query) => $query->limit($limit))
            ->get()
            ->map(fn (GalleryItem $item): array => [
                'id' => $item->id,
                'caption' => $item->caption,
                'image_url' => $item->imageUrl(),
            ])
            ->all();
    }
}
