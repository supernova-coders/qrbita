<?php

namespace Database\Factories;

use App\AiGenerationStatus;
use App\Models\AiGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiGeneration>
 */
class AiGenerationFactory extends Factory
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
            'v_card_id' => null,
            'prompt' => fake()->sentence(),
            'result' => ['public_name' => fake()->name()],
            'model' => 'gpt-4o-mini',
            'status' => AiGenerationStatus::Completed,
            'error_message' => null,
        ];
    }
}
