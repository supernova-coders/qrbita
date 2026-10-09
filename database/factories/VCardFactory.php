<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VCard;
use App\VCardStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VCard>
 */
class VCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'v_card_template_id' => null,
            'slug' => Str::lower(Str::random(12)),
            'title' => fake()->jobTitle().' · vCard',
            'public_name' => fake()->name(),
            'organization' => fake()->company(),
            'job_title' => fake()->jobTitle(),
            'phone' => fake()->phoneNumber(),
            'public_email' => fake()->safeEmail(),
            'website' => fake()->url(),
            'description' => fake()->sentence(16),
            'photo_path' => null,
            'cover_path' => null,
            'logo_path' => null,
            'accent_color' => '#000000',
            'status' => VCardStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VCardStatus::Published,
            'published_at' => now(),
        ]);
    }
}
