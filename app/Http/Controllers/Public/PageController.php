<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        $page = Page::forKey('about');

        return Inertia::render('Public/About', [
            'page' => [
                'title' => $page->title,
                'intro' => $page->intro,
                'body_html' => Str::safeMarkdown($page->body),
            ],
        ]);
    }
}
