<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        $values = Setting::values();

        return Inertia::render('Admin/Settings/Edit', [
            'settings' => [
                ...array_intersect_key($values, array_flip(['contact_instagram', 'contact_whatsapp', 'contact_youtube', 'contact_address'])),
                'registration_open' => $values['registration_open'] === '1',
            ],
        ]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        Setting::store([
            ...$request->safe()->except('registration_open'),
            'registration_open' => $request->boolean('registration_open') ? '1' : '0',
        ]);

        return to_route('admin.settings.edit')->with('status', 'Pengaturan disimpan.');
    }
}
