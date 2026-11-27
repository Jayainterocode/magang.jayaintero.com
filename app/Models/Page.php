<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Editable fixed pages, addressed by key. The keys and their defaults live in PageSeeder.
 */
#[Fillable(['key', 'title', 'intro', 'body'])]
class Page extends Model
{
    public const Keys = [
        'home' => 'Beranda',
        'about' => 'Tentang Magang',
    ];

    public static function forKey(string $key): self
    {
        return static::query()->where('key', $key)->firstOrNew(
            ['key' => $key],
            ['title' => self::Keys[$key] ?? $key, 'intro' => null, 'body' => ''],
        );
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }
}
