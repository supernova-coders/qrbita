<?php

use App\Models\User;
use App\UserStatus;
use Inertia\Testing\AssertableInertia as Assert;

test('regular users cannot access user administration', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('an administrator can view and deactivate another user', function () {
    $administrator = User::factory()->administrator()->create();
    $user = User::factory()->create();

    $this->actingAs($administrator)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/index')
            ->has('users.data', 2));

    $this->actingAs($administrator)
        ->patch(route('admin.users.status.update', $user), [
            'status' => UserStatus::Inactive->value,
        ])
        ->assertRedirect();

    expect($user->refresh()->status)->toBe(UserStatus::Inactive);
});

test('an administrator cannot deactivate their own account', function () {
    $administrator = User::factory()->administrator()->create();

    $this->actingAs($administrator)
        ->patch(route('admin.users.status.update', $administrator), [
            'status' => UserStatus::Inactive->value,
        ])
        ->assertSessionHasErrors('status');

    expect($administrator->refresh()->status)->toBe(UserStatus::Active);
});

test('an existing user can be promoted with the qrbita command', function () {
    $user = User::factory()->create();

    $this->artisan('qrbita:make-admin', ['email' => $user->email])
        ->assertSuccessful();

    expect($user->refresh()->isAdministrator())->toBeTrue();
});
