<?php

use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->editor = User::factory()->create();
});

test('editors can publish an article with a cover image', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.posts.store'), [
            'title' => 'Hari pertama magang',
            'category' => 'Kegiatan',
            'excerpt' => 'Cerita hari pertama.',
            'body' => 'Isi artikel.',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'is_published' => '1',
        ])
        ->assertRedirect(route('admin.posts.index'));

    $post = Post::sole();

    expect($post->slug)->toBe('hari-pertama-magang')
        ->and($post->isPublished())->toBeTrue()
        ->and($post->user_id)->toBe($this->editor->id);
    Storage::disk('public')->assertExists($post->cover_image);
});

test('replacing an article cover deletes the old file', function () {
    $oldCover = UploadedFile::fake()->image('old.jpg')->store('posts', 'public');
    $post = Post::factory()->create(['cover_image' => $oldCover]);

    $this->actingAs($this->editor)
        ->put(route('admin.posts.update', $post), [
            ...$post->only('title', 'category', 'excerpt', 'body'),
            'cover_image' => UploadedFile::fake()->image('new.jpg'),
            'is_published' => '1',
        ])
        ->assertRedirect(route('admin.posts.index'));

    Storage::disk('public')->assertMissing($oldCover);
    Storage::disk('public')->assertExists($post->fresh()->cover_image);
});

test('republishing keeps the original publish date and unpublishing clears it', function () {
    $post = Post::factory()->create(['published_at' => now()->subWeek()]);
    $originalDate = $post->published_at->toDateTimeString();
    $fields = $post->only('title', 'category', 'excerpt', 'body');

    $this->actingAs($this->editor)->put(route('admin.posts.update', $post), [...$fields, 'is_published' => '1']);
    expect($post->fresh()->published_at->toDateTimeString())->toBe($originalDate);

    $this->actingAs($this->editor)->put(route('admin.posts.update', $post), $fields);
    expect($post->fresh()->published_at)->toBeNull();
});

test('article covers must be images under 2 MB', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.posts.store'), [
            'title' => 'Judul',
            'category' => 'Kegiatan',
            'excerpt' => 'Ringkasan',
            'body' => 'Isi',
            'cover_image' => UploadedFile::fake()->create('cover.pdf', 3000, 'application/pdf'),
        ])
        ->assertSessionHasErrors('cover_image');
});

test('editors can manage faqs', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.faqs.store'), [
            'category' => 'Pendaftaran',
            'question' => 'Apakah magang dibayar?',
            'answer' => 'Jawaban.',
            'sort_order' => 1,
        ])
        ->assertRedirect(route('admin.faqs.index'));

    $faq = Faq::sole();
    expect($faq->is_published)->toBeFalse();

    $this->actingAs($this->editor)->delete(route('admin.faqs.destroy', $faq));
    expect(Faq::count())->toBe(0);
});

test('gallery photos are stored and removed with their file', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.gallery.store'), [
            'image' => UploadedFile::fake()->image('kegiatan.jpg'),
            'caption' => 'Survei lokasi proyek',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.gallery.index'));

    $item = GalleryItem::sole();
    Storage::disk('public')->assertExists($item->image);

    $this->actingAs($this->editor)->delete(route('admin.gallery.destroy', $item));

    Storage::disk('public')->assertMissing($item->image);
    expect(GalleryItem::count())->toBe(0);
});

test('a gallery photo requires an image when created', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.gallery.store'), ['caption' => 'Tanpa foto', 'sort_order' => 0])
        ->assertSessionHasErrors('image');
});

test('editors can update a fixed page', function () {
    $this->actingAs($this->editor)
        ->put(route('admin.pages.update', 'about'), [
            'title' => 'Tentang Magang',
            'intro' => 'Pengantar baru',
            'body' => '## Bidang kerja',
        ])
        ->assertRedirect(route('admin.pages.index'));

    expect(Page::forKey('about')->intro)->toBe('Pengantar baru');
});

test('unknown page keys are not editable', function () {
    $this->actingAs($this->editor)->get(route('admin.pages.edit', 'unknown'))->assertNotFound();
});

test('validation messages are in Indonesian', function () {
    $this->actingAs($this->editor)
        ->post(route('admin.faqs.store'), ['category' => 'Pendaftaran', 'answer' => 'x', 'sort_order' => 1])
        ->assertSessionHasErrors(['question' => 'Pertanyaan wajib diisi.']);
});
