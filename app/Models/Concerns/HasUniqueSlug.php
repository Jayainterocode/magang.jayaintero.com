<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills a unique `slug` from `title` when a model is created without one.
 */
trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->slug)) {
                $model->slug = static::uniqueSlugFor($model->title);
            }
        });
    }

    public static function uniqueSlugFor(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
