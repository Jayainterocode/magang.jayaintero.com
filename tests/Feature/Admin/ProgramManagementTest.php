<?php

use App\Enums\EducationLevel;
use App\Models\Application;
use App\Models\Program;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @return array<string, mixed>
 */
function validProgramData(array $overrides = []): array
{
    return [
        'title' => 'Magang Desain Interior',
        'division' => 'Desain Interior',
        'education_levels' => ['smk', 's1'],
        'summary' => 'Belajar membuat desain interior untuk proyek renovasi.',
        'description' => 'Deskripsi program.',
        'qualifications' => '- Jurusan desain',
        'duration_months' => 3,
        'quota' => 4,
        'opens_at' => '2026-12-01',
        'closes_at' => '2026-12-31',
        'is_published' => '1',
        ...$overrides,
    ];
}

test('editors cannot manage programs', function () {
    $editor = User::factory()->create();

    $this->actingAs($editor)->get(route('admin.programs.index'))->assertForbidden();
    $this->actingAs($editor)->post(route('admin.programs.store'), validProgramData())->assertForbidden();
});

test('admins see the program list', function () {
    Program::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.programs.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Programs/Index')
            ->has('programs.data', 2)
        );
});

test('admins can create a program with a generated slug', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.programs.store'), validProgramData())
        ->assertRedirect(route('admin.programs.index'));

    $program = Program::sole();

    expect($program->slug)->toBe('magang-desain-interior')
        ->and($program->is_published)->toBeTrue()
        ->and($program->education_levels->all())->toBe([EducationLevel::Smk, EducationLevel::S1]);
});

test('duplicate titles get a unique slug', function () {
    Program::factory()->create(['title' => 'Magang Desain Interior', 'slug' => 'magang-desain-interior']);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.programs.store'), validProgramData());

    expect(Program::latest('id')->first()->slug)->toBe('magang-desain-interior-2');
});

test('program validation rejects bad input', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.programs.store'), validProgramData([
            'title' => '',
            'education_levels' => ['sd'],
            'closes_at' => '2026-11-01',
        ]))
        ->assertSessionHasErrors(['title', 'education_levels.0', 'closes_at']);

    expect(Program::count())->toBe(0);
});

test('an unchecked publish box saves the program as a draft', function () {
    $program = Program::factory()->create();
    $data = validProgramData();
    unset($data['is_published']);

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.programs.update', $program), $data)
        ->assertRedirect(route('admin.programs.index'));

    expect($program->fresh()->is_published)->toBeFalse()
        ->and($program->fresh()->title)->toBe('Magang Desain Interior');
});

test('a program with applications cannot be deleted', function () {
    $program = Program::factory()->create();
    Application::factory()->for($program)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->from(route('admin.programs.index'))
        ->delete(route('admin.programs.destroy', $program))
        ->assertRedirect(route('admin.programs.index'))
        ->assertSessionHas('status');

    expect(Program::count())->toBe(1);
});

test('admins can delete a program', function () {
    $program = Program::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.programs.destroy', $program))
        ->assertRedirect(route('admin.programs.index'));

    expect(Program::count())->toBe(0);
});
