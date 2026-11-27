<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Faq', [
            'faqs' => self::published(),
        ]);
    }

    /**
     * @return list<array{id: int, category: string, question: string, answer_html: string}>
     */
    public static function published(?int $limit = null): array
    {
        return Faq::query()
            ->published()
            ->ordered()
            ->when($limit, fn ($query) => $query->limit($limit))
            ->get()
            ->map(fn (Faq $faq): array => [
                'id' => $faq->id,
                'category' => $faq->category,
                'question' => $faq->question,
                'answer_html' => Str::safeMarkdown($faq->answer),
            ])
            ->all();
    }
}
