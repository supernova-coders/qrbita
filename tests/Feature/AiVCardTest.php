<?php

use App\AiGenerationStatus;
use App\Models\AiGeneration;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('the ai assistant returns a constrained proposal and records the generation', function () {
    config([
        'services.openai.api_key' => 'test-key',
        'services.openai.model' => 'gpt-6-luna',
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
        'description' => 'Arquitectura humana y sostenible',
        'links' => [],
    ];
    Http::fake([
        'api.openai.com/v1/responses' => Http::response([
            'status' => 'completed',
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
            'current' => [
                'public_name' => 'Ana López',
                'links' => [[
                    'type' => 'linkedin',
                    'label' => 'LinkedIn',
                    'url' => 'https://www.linkedin.com/in/ana',
                ]],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('proposal.public_name', 'Ana López');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.openai.com/v1/responses'
        && $request['model'] === 'gpt-6-luna'
        && $request['store'] === false
        && $request['reasoning']['effort'] === 'none'
        && str_contains($request['input'], 'www.linkedin.com/in/ana')
        && $request['text']['format']['type'] === 'json_schema');
    $generation = AiGeneration::query()->sole();
    expect($generation->status)->toBe(AiGenerationStatus::Completed)
        ->and($generation->result['public_name'])->toBe('Ana López');
});

test('the assistant rejects active content and deceptive social links', function (array $changes) {
    config([
        'services.openai.api_key' => 'test-key',
        'services.openai.model' => 'gpt-6-luna',
        'services.openai.base_url' => 'https://api.openai.com/v1',
    ]);
    $proposal = [
        'title' => 'Perfil de Ana',
        'public_name' => 'Ana López',
        'organization' => null,
        'job_title' => null,
        'phone' => null,
        'public_email' => null,
        'website' => null,
        'description' => 'Perfil profesional',
        'links' => [],
        ...$changes,
    ];
    Http::fake(['api.openai.com/v1/responses' => Http::response([
        'status' => 'completed',
        'output' => [['content' => [['type' => 'output_text', 'text' => json_encode($proposal)]]]],
    ])]);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('vcard-ai.proposals.store'), [
            'prompt' => 'Crea una vCard profesional para Ana con estos datos.',
        ])
        ->assertStatus(503)
        ->assertJsonMissingPath('proposal');

    expect(AiGeneration::query()->sole()->status)->toBe(AiGenerationStatus::Failed);
})->with([
    'script in description' => [['description' => '<script>alert(1)</script>']],
    'symbols in public name' => [['public_name' => 'Ana//$']],
    'deceptive social link' => [['links' => [[
        'type' => 'linkedin',
        'label' => 'LinkedIn',
        'url' => 'https://linkedin.com.ejemplo-malicioso.test/in/ana',
    ]]]],
]);

test('the assistant limits requests per user before calling OpenAI', function () {
    config(['services.openai.api_key' => 'test-key', 'services.openai.daily_limit' => 1]);
    Http::preventStrayRequests();
    $user = User::factory()->create();
    AiGeneration::factory()->for($user)->create();

    $this->actingAs($user)
        ->postJson(route('vcard-ai.proposals.store'), [
            'prompt' => 'Crea una vCard profesional para Ana con estos datos.',
        ])
        ->assertStatus(429);

    Http::assertNothingSent();
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
