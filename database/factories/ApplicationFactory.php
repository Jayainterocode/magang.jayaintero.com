<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;
use App\Enums\Gender;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = now()->addWeeks(fake()->numberBetween(2, 8))->startOfDay();

        return [
            'program_id' => Program::factory(),
            'status' => ApplicationStatus::Submitted,
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08'.fake()->numerify('##########'),
            'birth_date' => fake()->dateTimeBetween('-24 years', '-16 years'),
            'gender' => fake()->randomElement(Gender::cases()),
            'address' => fake()->address(),
            'education_level' => fake()->randomElement(EducationLevel::cases()),
            'institution' => 'Universitas '.fake()->city(),
            'major' => fake()->randomElement(['Desain Interior', 'Teknik Sipil', 'Arsitektur', 'Manajemen']),
            'semester' => (string) fake()->numberBetween(3, 8),
            'student_number' => fake()->numerify('##########'),
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addMonths(3),
            'motivation' => fake()->paragraph(),
            'consent_at' => now(),
        ];
    }

    public function status(ApplicationStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
