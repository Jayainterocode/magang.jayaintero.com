<?php

namespace App\Http\Requests\Admin;

use App\Enums\EducationLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],
            'division' => ['required', 'string', 'max:100'],
            'education_levels' => ['required', 'array', 'min:1'],
            'education_levels.*' => [Rule::enum(EducationLevel::class)],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:10000'],
            'qualifications' => ['required', 'string', 'max:5000'],
            'duration_months' => ['required', 'integer', 'between:1,12'],
            'quota' => ['nullable', 'integer', 'between:1,500'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'is_published' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'division' => 'divisi',
            'education_levels' => 'jenjang',
            'summary' => 'ringkasan',
            'description' => 'deskripsi',
            'qualifications' => 'kualifikasi',
            'duration_months' => 'durasi',
            'quota' => 'kuota',
            'opens_at' => 'tanggal buka',
            'closes_at' => 'tanggal tutup',
        ];
    }
}
