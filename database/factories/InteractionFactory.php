<?php

namespace Database\Factories;

use App\InteractionType;
use App\Models\Interaction;
use App\Models\VCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
class InteractionFactory extends Factory
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
            'qr_code_id' => null,
            'v_card_link_id' => null,
            'event_type' => InteractionType::View,
            'country' => null,
            'city' => null,
            'device' => 'desktop',
            'browser' => 'other',
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'occurred_at' => now(),
        ];
    }
}
