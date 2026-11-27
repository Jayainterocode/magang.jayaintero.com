<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Admins handle programs, applicants, and site settings.
         * Editors only manage public content (pages, articles, FAQ, gallery).
         */
        Gate::define('manage-recruitment', fn (User $user): bool => $user->isAdmin());

        // Complements the per-email lockout in LoginRequest: caps guesses across many accounts from one device.
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(20)->by($request->ip()));
        RateLimiter::for('applications', fn (Request $request): Limit => Limit::perHour(5)->by($request->ip()));
        RateLimiter::for('status-check', fn (Request $request): Limit => Limit::perMinute(10)->by($request->ip()));

        /*
         * Markdown written in the admin is rendered with raw HTML stripped,
         * so the output is safe to print with {@html} in Svelte.
         */
        Str::macro('safeMarkdown', fn (?string $markdown): string => Str::markdown((string) $markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }
}
