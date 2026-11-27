<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\EducationLevel;
use App\Enums\Gender;
use App\Models\Application;
use App\Models\Program;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|Closure|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Honeypot: hidden from people, filled in by most spam bots.
            'website' => ['prohibited'],

            'program_id' => ['required', 'integer', $this->programIsAcceptingApplications(...), $this->noActiveApplicationForProgram(...)],

            'full_name' => ['required', 'string', 'max:150', "regex:/^[\\pL\\pM\\s.,'\\-]+$/u"],
            'email' => ['required', 'string', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +\-]{8,}$/'],
            'birth_date' => ['required', 'date', 'before:-14 years'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'address' => ['required', 'string', 'max:500'],

            'education_level' => ['required', Rule::enum(EducationLevel::class), $this->educationLevelMatchesProgram(...)],
            'institution' => ['required', 'string', 'max:150'],
            'major' => ['required', 'string', 'max:150'],
            'semester' => ['required', 'string', 'max:30'],
            'student_number' => ['required', 'string', 'max:50'],
            'supervisor_name' => ['nullable', 'string', 'max:150'],
            'supervisor_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +\-]{8,}$/'],

            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'motivation' => ['required', 'string', 'min:30', 'max:2000'],

            ...collect(DocumentType::cases())->mapWithKeys(fn (DocumentType $type): array => [$type->value => $type->rules()])->all(),

            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'program_id' => 'program',
            'full_name' => 'nama lengkap',
            'phone' => 'nomor WhatsApp',
            'birth_date' => 'tanggal lahir',
            'gender' => 'jenis kelamin',
            'address' => 'alamat',
            'education_level' => 'jenjang',
            'institution' => 'sekolah/kampus',
            'major' => 'jurusan',
            'semester' => 'kelas/semester',
            'student_number' => 'NIS/NIM',
            'supervisor_name' => 'nama pembimbing',
            'supervisor_phone' => 'kontak pembimbing',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'motivation' => 'motivasi',
            'cv' => 'CV',
            'cover_letter' => 'surat pengantar',
            'photo' => 'pas foto',
            'portfolio' => 'portofolio',
            'consent' => 'persetujuan data',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'birth_date.before' => 'Usia pelamar minimal 14 tahun.',
            'full_name.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, apostrof, atau tanda hubung.',
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, + atau -.',
            'supervisor_phone.regex' => 'Kontak pembimbing hanya boleh berisi angka, spasi, + atau -.',
            'consent.accepted' => 'Centang persetujuan penggunaan data untuk melanjutkan.',
            'website.prohibited' => 'Lamaran tidak dapat diproses.',
        ];
    }

    private function programIsAcceptingApplications(string $attribute, mixed $value, Closure $fail): void
    {
        $program = Program::query()->find($value);

        if (! $program?->isAcceptingApplications()) {
            $fail('Program ini sedang tidak menerima lamaran.');
        }
    }

    private function educationLevelMatchesProgram(string $attribute, mixed $value, Closure $fail): void
    {
        $program = Program::query()->find($this->input('program_id'));

        if ($program && ! $program->education_levels->contains(EducationLevel::tryFrom((string) $value))) {
            $fail('Program ini hanya untuk jenjang '.$program->education_levels->map->label()->join(', ').'.');
        }
    }

    private function noActiveApplicationForProgram(string $attribute, mixed $value, Closure $fail): void
    {
        $hasActiveApplication = Application::query()
            ->where('program_id', $value)
            ->where('email', $this->string('email')->lower()->toString())
            ->whereIn('status', ApplicationStatus::active())
            ->exists();

        if ($hasActiveApplication) {
            $fail('Email ini sudah punya lamaran aktif untuk program ini. Cek statusnya di halaman Cek Status.');
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
        }
    }
}
