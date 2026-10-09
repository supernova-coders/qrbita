<?php

use App\AiGenerationStatus;
use App\Models\AiGeneration;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('the ai assistant returns a constrained proposal and records the generation', function () {
    config([
        'services.openai.api_key' => 'test-key',
        'services.openai.model' => 'gpt-4o-mini',
        'services.openai.base_url' => 'https://api.openai.com/v1',
    ]);
    $proposal = [
        'title' => 'Perfil de Ana',
        'public_name' => 'Ana López',
        'organization' => 'Estudio Norte',
        'job_title' => 'Arquitecta',
        'phone' => null,
        'public_email' => 'ana@example.com',
        'website' => null,
        'description' => 'Arquitectura humana y sostenible.',
        'links' => [],
    ];
    Http::fake([
        'api.openai.com/v1/responses' => Http::response([
            'output' => [[
                'content' => [[
                    'type' => 'output_text',
                    'text' => json_encode($proposal),
                ]],
            ]],
        ]),
    ]);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('vcard-ai.proposals.store'), [
            'prompt' => 'Crea una vCard profesional con estos datos de Ana.',
            'current' => ['public_name' => 'Ana López'],
        ])
        ->assertOk()
        ->assertJsonPath('proposal.public_name', 'Ana López');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.openai.com/v1/responses'
        && $request['model'] === 'gpt-4o-mini'
        && $request['store'] === false
        && $request['text']['format']['type'] === 'json_schema');
    $generation = AiGeneration::query()->sole();
    expect($generation->status)->toBe(AiGenerationStatus::Completed)
        ->and($generation->result['public_name'])->toBe('Ana López');
});

test('the ai assistant fails safely when no api key is configured', function () {
    config(['services.openai.api_key' => null]);
    Http::preventStrayRequests();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('vcard-ai.proposals.store'), [
            'prompt' => 'Ayúdame a crear una presentación profesional breve.',
        ])
        ->assertStatus(503)
        ->assertJsonPath(
            'message',
            'La asistencia de IA no está disponible en este momento. Puedes continuar con el formulario manual.',
        );

    expect(AiGeneration::query()->sole()->status)
        ->toBe(AiGenerationStatus::Failed);
});
