<?php

namespace Database\Factories;

use App\Enums\EducationLevel;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Magang '.fake()->unique()->jobTitle();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'division' => fake()->randomElement(['Desain Interior', 'Drafter', 'Teknik Sipil', 'Marketing', 'Administrasi']),
            'education_levels' => fake()->randomElements(EducationLevel::cases(), 2),
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraphs(2, true),
            'qualifications' => '- '.implode("\n- ", fake()->sentences(3)),
            'duration_months' => fake()->numberBetween(1, 6),
            'quota' => fake()->numberBetween(2, 10),
            'opens_at' => now()->subWeek(),
            'closes_at' => now()->addMonth(),
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => false]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'opens_at' => now()->subMonths(2),
            'closes_at' => now()->subDay(),
        ]);
    }
}
