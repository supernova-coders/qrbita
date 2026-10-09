<?php

use App\InteractionType;
use App\Models\QrCode;
use App\Models\VCard;
use App\Models\VCardLink;
use Inertia\Testing\AssertableInertia as Assert;

test('only a published vcard is publicly visible', function () {
    $draft = VCard::factory()->create();

    $this->get(route('public.vcards.show', $draft))->assertNotFound();

    $published = VCard::factory()->published()->create();

    $this->get(route('public.vcards.show', $published))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/vcard')
            ->where('vCard.slug', $published->slug)
            ->where('vCard.public_name', $published->public_name));

    $this->assertDatabaseHas('interactions', [
        'v_card_id' => $published->id,
        'event_type' => InteractionType::View->value,
    ]);
});

test('a qr redirect records a scan and opens the published vcard', function () {
    $vCard = VCard::factory()->published()->create();
    $qrCode = QrCode::factory()->for($vCard)->create();

    $this->get(route('public.qr.redirect', $qrCode))
        ->assertRedirect(route('public.vcards.show', $vCard));

    $this->assertDatabaseHas('interactions', [
        'v_card_id' => $vCard->id,
        'qr_code_id' => $qrCode->id,
        'event_type' => InteractionType::Scan->value,
    ]);
});

test('a visitor can download a standards based contact file', function () {
    $vCard = VCard::factory()->published()->create([
        'public_name' => 'Ana López',
        'phone' => '+502 5555 0000',
        'public_email' => 'ana@example.com',
    ]);

    $response = $this->get(route('public.vcards.contact', $vCard));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/vcard; charset=utf-8')
        ->assertSee('BEGIN:VCARD', false)
        ->assertSee('FN:Ana López', false);
    $this->assertDatabaseHas('interactions', [
        'v_card_id' => $vCard->id,
        'event_type' => InteractionType::ContactSave->value,
    ]);
});

test('an active social link records a click before redirecting', function () {
    $vCard = VCard::factory()->published()->create();
    $link = VCardLink::factory()->for($vCard)->create([
        'type' => 'linkedin',
        'url' => 'https://www.linkedin.com/in/ana',
    ]);

    $this->get(route('public.vcards.links.redirect', [$vCard, $link]))
        ->assertRedirect('https://www.linkedin.com/in/ana');

    $this->assertDatabaseHas('interactions', [
        'v_card_id' => $vCard->id,
        'v_card_link_id' => $link->id,
        'event_type' => InteractionType::LinkClick->value,
    ]);
});

test('the owner can render a png qr code', function () {
    $vCard = VCard::factory()->published()->create();

    $response = $this->actingAs($vCard->user)
        ->get(route('vcards.qr', $vCard));

    $response->assertOk()->assertHeader('Content-Type', 'image/png');
    expect(substr($response->getContent(), 1, 3))->toBe('PNG');
});
