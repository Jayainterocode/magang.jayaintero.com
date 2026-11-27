<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;
use App\Enums\Gender;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'program_id', 'status', 'full_name', 'email', 'phone', 'birth_date', 'gender', 'address',
    'education_level', 'institution', 'major', 'semester', 'student_number',
    'supervisor_name', 'supervisor_phone', 'start_date', 'end_date', 'motivation', 'consent_at',
])]
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'gender' => Gender::class,
            'education_level' => EducationLevel::class,
            'birth_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'consent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        /*
         * The code is derived from the auto-increment id, so it is assigned
         * right after the insert: MG-2026-0001, MG-2026-0002, ...
         */
        static::created(function (self $application): void {
            $application->code = sprintf('MG-%s-%04d', $application->created_at->format('Y'), $application->id);
            $application->saveQuietly();
        });
    }

    /**
     * Admin list filters, shared by the table and the CSV export.
     *
     * @param  array{search?: ?string, program_id?: int|string|null, status?: ?string, education_level?: ?string}  $filters
     */
    #[Scope]
    protected function filter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('institution', 'like', "%{$search}%");
                });
            })
            ->when($filters['program_id'] ?? null, fn (Builder $query, $programId) => $query->where('program_id', $programId))
            ->when(
                ApplicationStatus::tryFrom((string) ($filters['status'] ?? '')),
                fn (Builder $query, ApplicationStatus $status) => $query->where('status', $status),
            )
            ->when(
                EducationLevel::tryFrom((string) ($filters['education_level'] ?? '')),
                fn (Builder $query, EducationLevel $level) => $query->where('education_level', $level),
            );
    }

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return HasMany<ApplicationDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    /**
     * @return HasMany<ApplicationStatusLog, $this>
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(ApplicationStatusLog::class);
    }
}
