<?php

namespace Database\Seeders;

use App\Models\VCardTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VCardTemplateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VCardTemplate::query()->updateOrCreate(
            ['name' => 'Órbita monocromática'],
            [
                'description' => 'Diseño profesional en blanco y negro optimizado para móviles.',
                'structure' => [
                    'theme' => 'monochrome',
                    'layout' => 'profile',
                    'supports' => ['photo', 'cover', 'logo', 'contact_links'],
                ],
                'is_active' => true,
            ],
        );
    }
}
