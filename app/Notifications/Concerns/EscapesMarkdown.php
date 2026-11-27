<?php

namespace App\Notifications\Concerns;

/**
 * Laravel renders mail lines as Markdown, so applicant-typed text such as
 * "[Klik di sini](https://…)" would become a real link in a Jaya Intero email.
 * Escape it before it goes into a line or greeting.
 */
trait EscapesMarkdown
{
    protected static function escapeMarkdown(string $text): string
    {
        return preg_replace('/([\\\\`*_{}\[\]()#+\-.!|<>~])/', '\\\\$1', $text);
    }
}
