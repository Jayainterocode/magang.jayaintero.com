<?php

use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\Program;
use App\Models\Setting;
use Inertia\Testing\AssertableInertia as Assert;

test('the home page renders with the public contact details', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('contact.whatsapp', config('jayaintero.settings.contact_whatsapp'))
            ->where('contact.whatsapp_link', 'https://wa.me/6282168639543')
            ->where('auth.user', null)
        );
});

test('the home page shows the editable hero and only open programs', function () {
    Page::create(['key' => 'home', 'title' => 'Judul hero baru', 'intro' => 'Pengantar', 'body' => 'Isi']);
    $open = Program::factory()->create();
    Program::factory()->closed()->create();
    Program::factory()->draft()->create();

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('hero.title', 'Judul hero baru')
            ->has('programs', 1)
            ->where('programs.0.slug', $open->slug)
        );
});

test('the home page limits faqs and posts to published items', function () {
    Faq::factory()->count(5)->create();
    Faq::factory()->hidden()->create();
    Post::factory()->count(2)->create();
    Post::factory()->draft()->create();

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('faqs', 4)
            ->has('posts', 2)
        );
});

test('contact details come from saved settings', function () {
    Setting::store(['contact_whatsapp' => '0812 0000 1111']);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('contact.whatsapp', '0812 0000 1111')
            ->where('contact.whatsapp_link', 'https://wa.me/6281200001111')
        );
});
