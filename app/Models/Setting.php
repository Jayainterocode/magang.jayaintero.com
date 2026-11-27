<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings edited from the admin "Pengaturan" screen.
 * Missing keys fall back to config('jayaintero.settings').
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    private const CacheKey = 'site-settings';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        $stored = Cache::rememberForever(
            self::CacheKey,
            fn (): array => static::query()->pluck('value', 'key')->all(),
        );

        return array_merge(config('jayaintero.settings'), $stored);
    }

    public static function value(string $key): ?string
    {
        return static::values()[$key] ?? null;
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public static function store(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CacheKey);
    }

    /**
     * Public contact details shared with every page.
     *
     * @return array{instagram: ?string, whatsapp: ?string, whatsapp_link: ?string, youtube: ?string, address: ?string}
     */
    public static function contact(): array
    {
        $values = static::values();
        $whatsappDigits = preg_replace('/\D+/', '', (string) $values['contact_whatsapp']);

        return [
            'instagram' => $values['contact_instagram'],
            'whatsapp' => $values['contact_whatsapp'],
            'whatsapp_link' => $whatsappDigits ? 'https://wa.me/62'.ltrim($whatsappDigits, '0') : null,
            'youtube' => $values['contact_youtube'],
            'address' => $values['contact_address'],
        ];
    }
}
