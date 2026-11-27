<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Access is enforced by the `can:manage-recruitment` route middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_instagram' => ['nullable', 'string', 'max:100'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +\-]+$/'],
            'contact_youtube' => ['nullable', 'string', 'max:100'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'registration_open' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'contact_instagram' => 'Instagram',
            'contact_whatsapp' => 'nomor WhatsApp',
            'contact_youtube' => 'YouTube',
            'contact_address' => 'alamat',
        ];
    }
}
