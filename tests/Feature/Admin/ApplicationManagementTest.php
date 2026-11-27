<?php

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\EducationLevel;
use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Notification::fake();
    $this->admin = User::factory()->admin()->create();
});

function applicationWithDocument(): Application
{
    Storage::fake('local');

    $application = Application::factory()->create();
    $path = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf')->store("applications/{$application->code}", 'local');
    $application->documents()->create(['type' => DocumentType::Cv, 'path' => $path, 'original_name' => 'cv-rina.pdf', 'size' => 102400]);

    return $application;
}

test('editors cannot see applications or their files', function () {
    $editor = User::factory()->create();
    $application = applicationWithDocument();
    $document = $application->documents->first();

    $this->actingAs($editor)->get(route('admin.applications.index'))->assertForbidden();
    $this->actingAs($editor)->get(route('admin.applications.show', $application))->assertForbidden();
    $this->actingAs($editor)->get(route('admin.applications.documents.show', [$application, $document]))->assertForbidden();
    $this->actingAs($editor)->get(route('admin.applications.export'))->assertForbidden();
});

test('guests cannot download applicant files', function () {
    $application = applicationWithDocument();

    $this->get(route('admin.applications.documents.show', [$application, $application->documents->first()]))
        ->assertRedirect(route('admin.login'));
});

test('admins can list and filter applications', function () {
    $program = Program::factory()->create();
    Application::factory()->for($program)->create(['full_name' => 'Rina Siregar', 'education_level' => EducationLevel::Smk]);
    Application::factory()->for($program)->status(ApplicationStatus::Accepted)->create(['full_name' => 'Budi Santoso']);
    Application::factory()->create(['full_name' => 'Sari Lubis']);

    $this->actingAs($this->admin)
        ->get(route('admin.applications.index'))
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Applications/Index')->has('applications.data', 3));

    $this->actingAs($this->admin)
        ->get(route('admin.applications.index', ['program_id' => $program->id, 'status' => 'accepted']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('applications.data', 1)
            ->where('applications.data.0.full_name', 'Budi Santoso')
            ->where('filters.status', 'accepted')
        );

    $this->actingAs($this->admin)
        ->get(route('admin.applications.index', ['search' => 'rina']))
        ->assertInertia(fn (Assert $page) => $page->has('applications.data', 1));
});

test('admins see the application detail with documents and history', function () {
    $application = applicationWithDocument();
    $application->statusLogs()->create(['to_status' => ApplicationStatus::Submitted, 'is_visible_to_applicant' => true]);

    $this->actingAs($this->admin)
        ->get(route('admin.applications.show', $application))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Applications/Show')
            ->where('application.code', $application->code)
            ->has('application.documents', 1)
            ->where('application.documents.0.original_name', 'cv-rina.pdf')
            ->has('application.history', 1)
            ->where('application.history.0.by', 'Pelamar')
        );
});

test('admins can open an applicant document', function () {
    $application = applicationWithDocument();

    $this->actingAs($this->admin)
        ->get(route('admin.applications.documents.show', [$application, $application->documents->first()]))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('a document cannot be opened through another application', function () {
    $application = applicationWithDocument();
    $other = Application::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.applications.documents.show', [$other, $application->documents->first()]))
        ->assertNotFound();
});

test('moving to interview logs the change and emails the applicant', function () {
    $application = Application::factory()->create();

    $this->actingAs($this->admin)
        ->from(route('admin.applications.show', $application))
        ->put(route('admin.applications.status.update', $application), [
            'status' => 'interview',
            'note' => 'Wawancara Senin, 7 Desember pukul 10.00 di kantor.',
            'is_visible_to_applicant' => '1',
        ])
        ->assertRedirect(route('admin.applications.show', $application))
        ->assertSessionHas('status');

    $application->refresh();
    $log = $application->statusLogs()->sole();

    expect($application->status)->toBe(ApplicationStatus::Interview)
        ->and($log->from_status)->toBe(ApplicationStatus::Submitted)
        ->and($log->user_id)->toBe($this->admin->id)
        ->and($log->is_visible_to_applicant)->toBeTrue();

    Notification::assertSentTo($application, ApplicationStatusChanged::class, function (ApplicationStatusChanged $notification) {
        return $notification->status === ApplicationStatus::Interview
            && $notification->note === 'Wawancara Senin, 7 Desember pukul 10.00 di kantor.';
    });
});

test('internal notes are not put in the applicant email', function () {
    $application = Application::factory()->create();

    $this->actingAs($this->admin)->put(route('admin.applications.status.update', $application), [
        'status' => 'rejected',
        'note' => 'Berkas kurang lengkap, catatan internal.',
    ]);

    Notification::assertSentTo($application, ApplicationStatusChanged::class, fn ($notification) => $notification->note === null);
});

test('screening does not email the applicant', function () {
    $application = Application::factory()->create();

    $this->actingAs($this->admin)->put(route('admin.applications.status.update', $application), ['status' => 'screening']);

    expect($application->fresh()->status)->toBe(ApplicationStatus::Screening);
    Notification::assertNothingSent();
});

test('a note can be added without changing the status, and no email is sent', function () {
    $application = Application::factory()->status(ApplicationStatus::Interview)->create();

    $this->actingAs($this->admin)->put(route('admin.applications.status.update', $application), [
        'status' => 'interview',
        'note' => 'Pelamar minta jadwal ulang.',
    ])->assertSessionHasNoErrors();

    expect($application->statusLogs()->count())->toBe(1);
    Notification::assertNothingSent();
});

test('saving the same status without a note is rejected', function () {
    $application = Application::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('admin.applications.status.update', $application), ['status' => 'submitted'])
        ->assertSessionHasErrors('status');

    expect($application->statusLogs()->count())->toBe(0);
});

test('the status email names the new status', function () {
    $application = Application::factory()->create();

    $mail = (new ApplicationStatusChanged($application, ApplicationStatus::Accepted, 'Mulai 4 Januari.'))->toMail($application);

    expect($mail->subject)->toContain('Diterima')
        ->and(implode(' ', $mail->introLines))->toContain('Mulai 4 Januari.');
});

test('the csv export follows the filters and neutralizes formulas', function () {
    $program = Program::factory()->create();
    Application::factory()->for($program)->create(['full_name' => '=HYPERLINK("x")']);
    Application::factory()->create(['full_name' => 'Tidak ikut']);

    $response = $this->actingAs($this->admin)->get(route('admin.applications.export', ['program_id' => $program->id]));

    $response->assertOk()->assertDownload();
    $csv = $response->streamedContent();

    expect($csv)->toStartWith("\xEF\xBB\xBF")
        ->toContain("'=HYPERLINK")
        ->not->toContain('Tidak ikut');
});

test('a program stops accepting applications once its quota of accepted applicants is filled', function () {
    $program = Program::factory()->create(['quota' => 2]);
    Application::factory()->for($program)->count(2)->status(ApplicationStatus::Accepted)->create();

    expect($program->isAcceptingApplications())->toBeFalse();

    $this->get(route('programs.show', $program))
        ->assertInertia(fn (Assert $page) => $page->where('program.is_open', false));
});

test('the dashboard shows application stats to admins only', function () {
    Application::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('applicationStats.total', 2)
            ->has('applicationStats.latest', 2)
        );

    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('applicationStats', null));
});
