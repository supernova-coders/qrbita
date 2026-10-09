<?php

namespace Database\Factories;

use App\Models\VCardTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VCardTemplate>
 */
class VCardTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'structure' => ['theme' => 'monochrome', 'layout' => 'profile'],
            'is_active' => true,
        ];
    }
}
