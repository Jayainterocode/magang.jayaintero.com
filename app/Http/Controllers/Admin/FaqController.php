<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Faqs/Index', [
            'faqs' => Faq::query()->ordered()->get(['id', 'category', 'question', 'sort_order', 'is_published']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Faqs/Form', [
            'faq' => null,
            'nextSortOrder' => (int) Faq::query()->max('sort_order') + 1,
        ]);
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
        ]);

        return to_route('admin.faqs.index')->with('status', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq): Response
    {
        return Inertia::render('Admin/Faqs/Form', [
            'faq' => $faq->only('id', 'category', 'question', 'answer', 'sort_order', 'is_published'),
            'nextSortOrder' => $faq->sort_order,
        ]);
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update([
            ...$request->validated(),
            'is_published' => $request->boolean('is_published'),
        ]);

        return to_route('admin.faqs.index')->with('status', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return to_route('admin.faqs.index')->with('status', 'FAQ dihapus.');
    }
}
