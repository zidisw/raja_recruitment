<?php

use App\Models\User;
use Laravel\Fortify\Fortify;

test('login shows specific error for non-existent email', function () {
    $response = $this->post(route('login.store'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        Fortify::username() => 'The email address you entered is not in our records.',
    ]);
});

test('login shows specific error for incorrect password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'The password you entered is incorrect. Please try again.',
    ]);
});

test('login works with correct credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('correct-password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});
