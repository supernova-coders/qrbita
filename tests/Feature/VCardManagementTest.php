<?php

use App\Models\User;
use App\Models\VCard;
use App\VCardStatus;
use Inertia\Testing\AssertableInertia as Assert;

function validVCardPayload(array $overrides = []): array
{
    return [
        'title' => 'Perfil profesional',
        'public_name' => 'Sara López',
        'organization' => 'Estudio Norte',
        'job_title' => 'Fotografa',
        'phone' => '+502 5555 0000',
        'public_email' => 'sara@example.com',
        'website' => 'https://example.com',
        'description' => 'Diseño espacios humanos funcionales y sostenibles',
        'links' => [
            [
                'type' => 'linkedin',
                'label' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/in/sara',
                'is_active' => true,
            ],
        ],
        ...$overrides,
    ];
}

test('an authenticated user can create one draft vcard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(
        route('vcards.store'),
        validVCardPayload(),
    );

    $vCard = $user->vCard()->firstOrFail();

    $response->assertRedirect(route('vcards.edit', $vCard));
    expect($vCard->status)->toBe(VCardStatus::Draft)
        ->and($vCard->accent_color)->toBe('#000000')
        ->and($vCard->links)->toHaveCount(1);
    $this->assertDatabaseHas('v_card_links', [
        'v_card_id' => $vCard->id,
        'type' => 'linkedin',
    ]);
});

test('the vcard form rejects active content and deceptive social links', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('vcards.store'), validVCardPayload([
            'description' => '<script>alert(1)</script>',
            'links' => [[
                'type' => 'linkedin',
                'label' => 'LinkedIn',
                'url' => 'https://linkedin.com.ejemplo-malicioso.test/in/sara',
            ]],
        ]))
        ->assertSessionHasErrors(['description', 'links.0.url']);

    expect($user->vCard()->count())->toBe(0);
});

test('the vcard form rejects symbols in personal information', function (string $field, string $value) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('vcards.store'), validVCardPayload([$field => $value]))
        ->assertSessionHasErrors([$field]);

    expect($user->vCard()->count())->toBe(0);
})->with([
    'title' => ['title', 'Mi vCard #1'],
    'public name' => ['public_name', 'Nicolás//$'],
    'organization' => ['organization', 'Empresa N%%'],
    'job title' => ['job_title', 'Desarrollador;...?'],
    'description' => ['description', 'Hola #mundo'],
]);

test('the vcard form accepts accented letters and umlauts', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('vcards.store'), validVCardPayload([
            'public_name' => 'Ana Müller',
            'organization' => 'Compañía Águila',
            'job_title' => 'Diseñadora',
            'description' => 'Diseño útil para la comunidad',
        ]))
        ->assertSessionHasNoErrors();

    expect($user->vCard()->firstOrFail()->public_name)->toBe('Ana Müller');
});

test('a user cannot create a second vcard', function () {
    $user = User::factory()->create();
    VCard::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('vcards.store'), validVCardPayload())
        ->assertForbidden();

    expect($user->vCard()->count())->toBe(1);
});

test('an owner can update content without changing the stable slug', function () {
    $user = User::factory()->create();
    $vCard = VCard::factory()->for($user)->create([
        'slug' => 'ana-estable-123456',
    ]);

    $this->actingAs($user)
        ->put(route('vcards.update', $vCard), validVCardPayload([
            'public_name' => 'Ana López Renovada',
        ]))
        ->assertRedirect();

    expect($vCard->refresh())
        ->slug->toBe('ana-estable-123456')
        ->public_name->toBe('Ana López Renovada');
});

test('an owner cannot save symbols in personal information', function () {
    $user = User::factory()->create();
    $vCard = VCard::factory()->for($user)->create(['public_name' => 'Ana López']);

    $this->actingAs($user)
        ->put(route('vcards.update', $vCard), validVCardPayload([
            'public_name' => 'Ana//$',
        ]))
        ->assertSessionHasErrors(['public_name']);

    expect($vCard->refresh()->public_name)->toBe('Ana López');
});

test('a user cannot edit another users vcard', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $vCard = VCard::factory()->for($owner)->create();

    $this->actingAs($otherUser)
        ->get(route('vcards.edit', $vCard))
        ->assertForbidden();
});

test('publishing creates one reusable qr code', function () {
    $user = User::factory()->create();
    $vCard = VCard::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('vcards.publication.store', $vCard))
        ->assertRedirect();
    $code = $vCard->qrCode()->firstOrFail()->code;

    $this->actingAs($user)
        ->delete(route('vcards.publication.destroy', $vCard))
        ->assertRedirect();
    $this->actingAs($user)
        ->post(route('vcards.publication.store', $vCard))
        ->assertRedirect();

    expect($vCard->refresh()->status)->toBe(VCardStatus::Published)
        ->and($vCard->qrCode()->count())->toBe(1)
        ->and($vCard->qrCode()->firstOrFail()->code)->toBe($code);
});

test('the editor returns the expected inertia data', function () {
    $user = User::factory()->create();
    $vCard = VCard::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('vcards.edit', $vCard))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('vcards/editor')
            ->where('vCard.slug', $vCard->slug)
            ->has('vCard.links')
            ->has('aiConfigured'));
});
