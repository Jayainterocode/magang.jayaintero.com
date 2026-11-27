<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['Pendaftaran', 'Selama Magang']),
            'question' => rtrim(fake()->unique()->sentence(8), '.').'?',
            'answer' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => ['is_published' => false]);
    }
}
