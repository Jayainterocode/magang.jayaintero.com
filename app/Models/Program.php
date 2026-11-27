<?php

namespace App\Models;

use App\Enums\EducationLevel;
use App\Models\Concerns\HasUniqueSlug;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title', 'slug', 'division', 'education_levels', 'summary', 'description',
    'qualifications', 'duration_months', 'quota', 'opens_at', 'closes_at', 'is_published',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, HasUniqueSlug;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'education_levels' => AsEnumCollection::of(EducationLevel::class),
            'opens_at' => 'date',
            'closes_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * The fields public program cards and the detail page share.
     *
     * @return array{title: string, slug: string, division: string, education_levels: list<string>, summary: string, duration_months: int, quota: ?int, opens_at: ?string, closes_at: ?string, is_open: bool}
     */
    public function toCardArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'division' => $this->division,
            'education_levels' => $this->education_levels->map->label()->values()->all(),
            'summary' => $this->summary,
            'duration_months' => $this->duration_months,
            'quota' => $this->quota,
            'opens_at' => $this->opens_at?->translatedFormat('j F Y'),
            'closes_at' => $this->closes_at?->translatedFormat('j F Y'),
            'is_open' => $this->isAcceptingApplications(),
        ];
    }

    /**
     * Open for applicants: the program is open and registration is switched on site-wide.
     */
    public function isAcceptingApplications(): bool
    {
        return $this->isOpen() && Setting::value('registration_open') === '1';
    }

    /**
     * Whether applications are accepted today: published and inside the open period.
     */
    public function isOpen(): bool
    {
        $today = now()->startOfDay();

        return $this->is_published
            && ($this->opens_at === null || $this->opens_at->lte($today))
            && ($this->closes_at === null || $this->closes_at->gte($today));
    }
}
