<?php

namespace Tests\Feature;

use App\Models\User;

class SessionLoginTest extends ApiTestCase
{
    public function test_temporary_session_login_allows_access_to_products_api(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $loginResponse = $this->post('/session-login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonPath('message', 'Sessão autenticada criada com sucesso.');

        $this->assertAuthenticated();

        $apiResponse = $this->getJson('/browser/products');

        $apiResponse
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_demo_bearer_token_allows_api_access_without_session(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer local-demo-token',
        ])->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}