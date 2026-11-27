<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * The image is required when adding a photo and optional when editing one.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [$this->route('gallery') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'between:0,999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'image' => 'foto',
            'caption' => 'keterangan',
            'sort_order' => 'urutan',
        ];
    }
}
