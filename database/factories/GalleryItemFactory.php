<?php

namespace Database\Factories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'gallery/'.fake()->uuid().'.jpg',
            'caption' => fake()->sentence(6),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
