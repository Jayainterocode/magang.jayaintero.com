<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected from the dashboard to the login page', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

test('the login page renders', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Admin/Auth/Login'));
});

test('staff can log in with valid credentials', function () {
    $admin = User::factory()->admin()->create();

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('login fails with a wrong password', function () {
    $admin = User::factory()->admin()->create();

    $this->from(route('admin.login'))
        ->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('login is locked after five failed attempts', function () {
    $admin = User::factory()->admin()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('logged in staff see the dashboard with their role', function () {
    $editor = User::factory()->create();

    $this->actingAs($editor)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->where('auth.user.email', $editor->email)
            ->where('auth.user.role', 'editor')
        );
});

test('logged in staff are sent from the login page to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

test('staff can log out', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
