<?php

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;

test('guest is redirected to login page when accessing dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@financee.local',
        'password' => 'secret123',
    ]);

    $response = $this->post('/login', [
        'email' => 'user@financee.local',
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertAuthenticatedAs($user);
});

test('user can register a new account', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@financee.local',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'budi@financee.local',
    ]);
});

test('dashboard loads successfully for authenticated user', function () {
    $user = User::factory()->create();

    Transaction::create([
        'user_id' => $user->id,
        'type' => 'pengeluaran',
        'category' => 'Makan',
        'title' => 'Makan Siang',
        'amount' => 35000,
        'date' => now()->format('Y-m-d'),
    ]);

    Budget::create([
        'user_id' => $user->id,
        'category' => 'Makan',
        'amount' => 500000,
        'month' => now()->format('Y-m'),
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Financee');
    $response->assertSee('Makan Siang');
    $response->assertSee($user->name);
});

test('data isolation: user B cannot see or manipulate user A transactions', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $trxA = Transaction::create([
        'user_id' => $userA->id,
        'type' => 'pengeluaran',
        'category' => 'Makan',
        'title' => 'Rahasia User A',
        'amount' => 50000,
        'date' => now()->format('Y-m-d'),
    ]);

    $budgetA = Budget::create([
        'user_id' => $userA->id,
        'category' => 'Makan',
        'amount' => 200000,
        'month' => now()->format('Y-m'),
    ]);

    // User B visits dashboard -> must NOT see User A's transaction
    $responseB = $this->actingAs($userB)->get('/');
    $responseB->assertStatus(200);
    $responseB->assertDontSee('Rahasia User A');

    // User B tries to delete User A's transaction -> must receive 403 Forbidden
    $deleteTrx = $this->actingAs($userB)->delete(route('transactions.destroy', $trxA));
    $deleteTrx->assertStatus(403);

    // User B tries to delete User A's budget -> must receive 403 Forbidden
    $deleteBudget = $this->actingAs($userB)->delete(route('budgets.destroy', $budgetA));
    $deleteBudget->assertStatus(403);
});

test('quick parse returns structured transaction data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/transactions/quick-parse', [
        'text' => 'makan coto makassar 39k',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'type' => 'pengeluaran',
        'category' => 'Makan',
        'amount' => 39000,
    ]);
});

test('user can export transactions to excel without type compatibility errors', function () {
    $user = User::factory()->create();

    Transaction::create([
        'user_id' => $user->id,
        'type' => 'pengeluaran',
        'category' => 'Makan',
        'title' => 'Nasi Padang',
        'description' => 'Makan siang enak',
        'amount' => 25000,
        'date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($user)->get(route('report.export'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // Test with filters
    $filterResponse = $this->actingAs($user)->get(route('report.export', [
        'bulan' => now()->format('Y-m'),
        'kategori' => 'Makan',
        'type' => 'pengeluaran',
    ]));
    $filterResponse->assertStatus(200);
    $filterResponse->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
