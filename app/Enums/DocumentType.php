<?php

namespace App\Enums;

enum DocumentType: string
{
    case Cv = 'cv';
    case CoverLetter = 'cover_letter';
    case Photo = 'photo';
    case Portfolio = 'portfolio';

    public function label(): string
    {
        return match ($this) {
            self::Cv => 'CV',
            self::CoverLetter => 'Surat pengantar',
            self::Photo => 'Pas foto',
            self::Portfolio => 'Portofolio',
        };
    }

    /**
     * Validation rules for the uploaded file, keyed by the form field name (the enum value).
     *
     * @return list<string>
     */
    public function rules(): array
    {
        return match ($this) {
            self::Cv, self::CoverLetter => ['required', 'file', 'mimes:pdf', 'max:2048'],
            self::Photo => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            self::Portfolio => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        };
    }
}
