<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\ApplicationSubmitted;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

test('an applicant name cannot inject a link into the confirmation email', function () {
    $application = Application::factory()->create(['full_name' => 'Rina [Verifikasi di sini](https://evil.example)']);

    $html = (string) (new ApplicationSubmitted($application))->toMail($application)->render();

    expect($html)->not->toContain('href="https://evil.example"')
        ->and($html)->toContain('Verifikasi di sini');
});

test('an applicant name cannot inject a link into the status email', function () {
    $application = Application::factory()->create(['full_name' => '[Klik](https://evil.example)']);

    $html = (string) (new ApplicationStatusChanged($application, ApplicationStatus::Accepted))->toMail($application)->render();

    expect($html)->not->toContain('href="https://evil.example"');
});

test('the application form rejects names with link or markup characters', function () {
    Storage::fake('local');
    Notification::fake();
    $program = Program::factory()->create();

    $this->post(route('applications.store'), [
        'program_id' => $program->id,
        'full_name' => 'Rina [Klik](https://evil.example)',
        'email' => 'victim@example.com',
        'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('full_name');
});

test('names with apostrophes, hyphens, and accents are accepted', function (string $name) {
    $validator = validator(['full_name' => $name], ['full_name' => ["regex:/^[\\pL\\pM\\s.,'\\-]+$/u"]]);

    expect($validator->passes())->toBeTrue();
})->with(["Siti Nur'aini", 'Anak Agung Ayu-Putri', 'M. Rizky, S.T.', 'José Simanjuntak']);

test('every page sends baseline security headers', function (string $url) {
    $this->get($url)
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
})->with(['/', '/admin/login']);

test('login attempts are capped per device across many accounts', function () {
    foreach (range(1, 20) as $attempt) {
        $this->post(route('admin.login.store'), ['email' => "user{$attempt}@example.com", 'password' => 'wrong']);
    }

    $admin = User::factory()->admin()->create();

    $this->from(route('admin.login'))
        ->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password'])
        ->assertSessionHasErrors('form');

    $this->assertGuest();
});

test('the demo seeder refuses to run in production', function () {
    app()->detectEnvironment(fn () => 'production');

    app(DatabaseSeeder::class)->run();

    expect(User::count())->toBe(0);
});
