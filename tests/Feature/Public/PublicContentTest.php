<?php

use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\Program;
use App\Models\Setting;
use Inertia\Testing\AssertableInertia as Assert;

test('the program list shows published programs only', function () {
    Program::factory()->count(2)->create();
    Program::factory()->draft()->create();

    $this->get(route('programs.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Programs/Index')
            ->has('programs', 2)
        );
});

test('a published program page renders its markdown as html', function () {
    $program = Program::factory()->create(['qualifications' => "- Jurusan teknik\n- Bisa AutoCAD"]);

    $this->get(route('programs.show', $program))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Programs/Show')
            ->where('program.title', $program->title)
            ->where('program.is_open', true)
            ->where('program.qualifications_html', fn (string $html) => str_contains($html, '<li>Bisa AutoCAD</li>'))
        );
});

test('a draft program is not publicly reachable', function () {
    $program = Program::factory()->draft()->create();

    $this->get(route('programs.show', $program))->assertNotFound();
});

test('a program past its closing date is shown as closed', function () {
    $program = Program::factory()->closed()->create();

    $this->get(route('programs.show', $program))
        ->assertInertia(fn (Assert $page) => $page->where('program.is_open', false));
});

test('switching registration off closes every program', function () {
    $program = Program::factory()->create();
    Setting::store(['registration_open' => '0']);

    $this->get(route('programs.show', $program))
        ->assertInertia(fn (Assert $page) => $page->where('program.is_open', false));
});

test('raw html in admin markdown is stripped before it reaches the page', function () {
    $program = Program::factory()->create(['description' => "Halo <script>alert('x')</script> dunia"]);

    $this->get(route('programs.show', $program))
        ->assertInertia(fn (Assert $page) => $page
            ->where('program.description_html', fn (string $html) => ! str_contains($html, '<script>'))
        );
});

test('published articles are listed and readable, drafts are not', function () {
    $published = Post::factory()->create();
    $draft = Post::factory()->draft()->create();

    $this->get(route('posts.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Posts/Index')
            ->has('posts.data', 1)
            ->where('posts.data.0.slug', $published->slug)
        );

    $this->get(route('posts.show', $published))->assertOk();
    $this->get(route('posts.show', $draft))->assertNotFound();
});

test('the faq page hides unpublished questions', function () {
    Faq::factory()->count(3)->create();
    Faq::factory()->hidden()->create();

    $this->get(route('faq'))
        ->assertInertia(fn (Assert $page) => $page->component('Public/Faq')->has('faqs', 3));
});

test('the about page shows the saved page content', function () {
    Page::create(['key' => 'about', 'title' => 'Tentang Magang', 'intro' => null, 'body' => '## Bidang kerja']);

    $this->get(route('about'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/About')
            ->where('page.body_html', fn (string $html) => str_contains($html, '<h2>Bidang kerja</h2>'))
        );
});

test('the gallery page renders', function () {
    $this->get(route('gallery'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Public/Gallery')->has('items', 0));
});
