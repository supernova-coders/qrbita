<?php

namespace Database\Factories;

use App\Models\QrCode;
use App\Models\VCard;
use App\QrCodeStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QrCode>
 */
class QrCodeFactory extends Factory
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
            'code' => Str::lower(Str::random(20)),
            'destination_url' => '/v/example',
            'image_url' => '/v/example/qr',
            'status' => QrCodeStatus::Active,
        ];
    }
}
