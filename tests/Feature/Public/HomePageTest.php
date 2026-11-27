<?php

use Inertia\Testing\AssertableInertia as Assert;

test('the home page renders with the public contact details', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('contact.whatsapp', config('jayaintero.contact.whatsapp'))
            ->where('auth.user', null)
        );
});
