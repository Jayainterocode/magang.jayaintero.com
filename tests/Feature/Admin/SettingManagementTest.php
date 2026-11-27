<?php

use App\Models\Setting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('editors cannot open site settings', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.settings.edit'))
        ->assertForbidden();
});

test('admins see current settings with config defaults', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.settings.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Settings/Edit')
            ->where('settings.contact_instagram', 'jayaintero.id')
            ->where('settings.registration_open', true)
        );
});

test('admins can update contacts and close registration', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.settings.update'), [
            'contact_instagram' => 'jayaintero.magang',
            'contact_whatsapp' => '0812 3456 7890',
            'contact_youtube' => 'JAYA INTERO',
            'contact_address' => 'Medan',
        ])
        ->assertRedirect(route('admin.settings.edit'));

    expect(Setting::value('contact_instagram'))->toBe('jayaintero.magang')
        ->and(Setting::value('registration_open'))->toBe('0');
});

test('the whatsapp number only accepts digits, spaces, plus and dashes', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.settings.update'), ['contact_whatsapp' => 'call me'])
        ->assertSessionHasErrors('contact_whatsapp');
});

test('the sidebar hides recruitment menus from editors', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.canManageRecruitment', false));

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.canManageRecruitment', true));
});
