<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Inertia\Testing\AssertableInertia as Assert;

test('the status page renders an empty lookup form', function () {
    $this->get(route('applications.status'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Applications/Status')
            ->where('result', null)
        );
});

test('a matching code and email shows the status and visible history only', function () {
    $application = Application::factory()->status(ApplicationStatus::Interview)->create(['email' => 'rina@example.com']);
    $application->statusLogs()->create(['to_status' => ApplicationStatus::Submitted, 'note' => 'Dikirim', 'is_visible_to_applicant' => true]);
    $application->statusLogs()->create(['to_status' => ApplicationStatus::Interview, 'note' => 'Catatan internal', 'is_visible_to_applicant' => false]);

    $this->post(route('applications.status.check'), [
        'code' => strtolower($application->code),
        'email' => ' RINA@example.com ',
    ])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('result.code', $application->code)
            ->where('result.status_label', 'Wawancara')
            ->has('result.timeline', 1)
            ->where('result.timeline.0.note', 'Dikirim')
        );
});

test('a wrong email gives the same not-found answer as a missing code', function () {
    $application = Application::factory()->create(['email' => 'rina@example.com']);

    $this->post(route('applications.status.check'), ['code' => $application->code, 'email' => 'other@example.com'])
        ->assertInertia(fn (Assert $page) => $page->where('result', null)->where('notFound', true));

    $this->post(route('applications.status.check'), ['code' => 'MG-2000-9999', 'email' => 'rina@example.com'])
        ->assertInertia(fn (Assert $page) => $page->where('result', null)->where('notFound', true));
});

test('status checks are rate limited', function () {
    foreach (range(1, 10) as $attempt) {
        $this->post(route('applications.status.check'), ['code' => 'MG-2000-0001', 'email' => 'x@example.com']);
    }

    $this->from(route('applications.status'))
        ->post(route('applications.status.check'), ['code' => 'MG-2000-0001', 'email' => 'x@example.com'])
        ->assertRedirect(route('applications.status'))
        ->assertSessionHasErrors('form');
});
