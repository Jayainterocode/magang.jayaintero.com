<?php

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\EducationLevel;
use App\Models\Application;
use App\Models\Program;
use App\Models\Setting;
use App\Notifications\ApplicationSubmitted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('local');
    Notification::fake();

    $this->program = Program::factory()->create([
        'education_levels' => [EducationLevel::Smk, EducationLevel::S1],
    ]);
});

/**
 * @return array<string, mixed>
 */
function validApplicationData(Program $program, array $overrides = []): array
{
    return [
        'program_id' => $program->id,
        'full_name' => 'Rina Siregar',
        'email' => 'Rina@Example.com',
        'phone' => '0812 3456 7890',
        'birth_date' => now()->subYears(17)->toDateString(),
        'gender' => 'female',
        'address' => 'Jl. Setia Budi, Medan',
        'education_level' => 'smk',
        'institution' => 'SMK Negeri 1 Medan',
        'major' => 'Desain Pemodelan',
        'semester' => 'Kelas XI',
        'student_number' => '2023001',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonths(4)->toDateString(),
        'motivation' => 'Saya ingin belajar langsung di proyek renovasi dan interior yang nyata.',
        'cv' => UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'),
        'cover_letter' => UploadedFile::fake()->create('surat.pdf', 300, 'application/pdf'),
        'photo' => UploadedFile::fake()->image('foto.jpg'),
        'consent' => '1',
        ...$overrides,
    ];
}

test('the form lists only programs that accept applications and preselects one', function () {
    Program::factory()->closed()->create();
    Program::factory()->draft()->create();

    $this->get(route('applications.create', ['program' => $this->program->slug]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Applications/Create')
            ->has('programs', 1)
            ->where('selectedProgramId', $this->program->id)
        );
});

test('an applicant can submit an application with documents', function () {
    $this->post(route('applications.store'), validApplicationData($this->program))
        ->assertRedirect(route('applications.submitted'))
        ->assertSessionHas('submittedApplicationCode');

    $application = Application::sole();

    expect($application->code)->toBe(sprintf('MG-%s-%04d', now()->format('Y'), $application->id))
        ->and($application->email)->toBe('rina@example.com')
        ->and($application->status)->toBe(ApplicationStatus::Submitted)
        ->and($application->consent_at)->not->toBeNull()
        ->and($application->documents)->toHaveCount(3)
        ->and($application->statusLogs)->toHaveCount(1);

    foreach ($application->documents as $document) {
        Storage::disk('local')->assertExists($document->path);
        expect($document->path)->toStartWith("applications/{$application->code}/");
    }

    Notification::assertSentTo($application, ApplicationSubmitted::class);
});

test('the optional portfolio is stored when provided', function () {
    $this->post(route('applications.store'), validApplicationData($this->program, [
        'portfolio' => UploadedFile::fake()->create('portofolio.pdf', 1000, 'application/pdf'),
    ]));

    expect(Application::sole()->documents->pluck('type'))->toContain(DocumentType::Portfolio);
});

test('the success page shows the code once, then sends visitors to status check', function () {
    $this->post(route('applications.store'), validApplicationData($this->program));
    $code = Application::sole()->code;

    $this->get(route('applications.submitted'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Applications/Submitted')
            ->where('application.code', $code)
        );

    $this->get(route('applications.submitted'))->assertRedirect(route('applications.status'));
});

test('required fields and documents are validated in Indonesian', function () {
    $this->post(route('applications.store'), validApplicationData($this->program, [
        'full_name' => '',
        'cv' => null,
        'consent' => null,
    ]))->assertSessionHasErrors([
        'full_name' => 'Nama lengkap wajib diisi.',
        'cv' => 'CV wajib diisi.',
        'consent' => 'Centang persetujuan penggunaan data untuk melanjutkan.',
    ]);

    expect(Application::count())->toBe(0);
});

test('documents must have the right type and size', function () {
    $this->post(route('applications.store'), validApplicationData($this->program, [
        'cv' => UploadedFile::fake()->create('cv.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        'photo' => UploadedFile::fake()->image('foto.jpg')->size(2000),
    ]))->assertSessionHasErrors(['cv', 'photo']);
});

test('applications to a closed program are rejected', function () {
    $closed = Program::factory()->closed()->create();

    $this->post(route('applications.store'), validApplicationData($closed))
        ->assertSessionHasErrors(['program_id' => 'Program ini sedang tidak menerima lamaran.']);
});

test('applications are rejected when registration is switched off', function () {
    Setting::store(['registration_open' => '0']);

    $this->post(route('applications.store'), validApplicationData($this->program))
        ->assertSessionHasErrors('program_id');
});

test('the education level must match the program', function () {
    $this->post(route('applications.store'), validApplicationData($this->program, ['education_level' => 'd3']))
        ->assertSessionHasErrors('education_level');
});

test('the same email cannot apply twice to a program while the first is active', function () {
    Application::factory()->for($this->program)->create(['email' => 'rina@example.com']);

    $this->post(route('applications.store'), validApplicationData($this->program))
        ->assertSessionHasErrors('program_id');
});

test('a rejected applicant may apply again', function () {
    Application::factory()->for($this->program)->status(ApplicationStatus::Rejected)->create(['email' => 'rina@example.com']);

    $this->post(route('applications.store'), validApplicationData($this->program))
        ->assertSessionHasNoErrors();

    expect(Application::count())->toBe(2);
});

test('the honeypot field blocks bots', function () {
    $this->post(route('applications.store'), validApplicationData($this->program, ['website' => 'https://spam.example']))
        ->assertSessionHasErrors('website');

    expect(Application::count())->toBe(0);
});

test('submissions are rate limited per device', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('applications.store'), ['program_id' => $this->program->id]);
    }

    $this->from(route('applications.create'))
        ->post(route('applications.store'), validApplicationData($this->program))
        ->assertRedirect(route('applications.create'))
        ->assertSessionHasErrors('form');

    expect(Application::count())->toBe(0);
});

test('uploaded documents are not publicly reachable', function () {
    $this->post(route('applications.store'), validApplicationData($this->program));
    $path = Application::sole()->documents->first()->path;

    expect($this->get('/storage/'.$path)->status())->toBeIn([403, 404]);
});

test('emails carry the Jaya Intero logo instead of the Laravel one', function () {
    $application = Application::factory()->for($this->program)->create();

    $html = (string) (new ApplicationSubmitted($application))->toMail($application)->render();

    expect($html)->toContain('images/logo-mark.png')
        ->not->toContain('laravel.com/img');
});

test('the confirmation email contains the application code', function () {
    $application = Application::factory()->for($this->program)->create();

    $mail = (new ApplicationSubmitted($application))->toMail($application);

    expect($mail->subject)->toContain($application->code)
        ->and(implode(' ', $mail->introLines))->toContain($application->code);
});
