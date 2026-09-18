<?php

use App\Models\User;

test('user can logout via post request', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('user can logout via get request without 405 error', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('unauthenticated user hitting logout redirects to login safely', function () {
    $response = $this->get('/logout');

    $response->assertRedirect('/login');
});
