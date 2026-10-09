<?php

namespace Database\Factories;

use App\Models\VCard;
use App\Models\VCardLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VCardLink>
 */
class VCardLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'v_card_id' => VCard::factory(),
            'type' => 'linkedin',
            'label' => 'LinkedIn',
            'url' => fake()->url(),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
