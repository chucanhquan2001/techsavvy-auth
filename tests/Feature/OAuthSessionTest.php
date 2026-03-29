<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OAuthSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_returns_unauthenticated_without_token(): void
    {
        $response = $this->getJson('/api/v1/oauth/session');

        $response
            ->assertOk()
            ->assertJsonPath('data.authenticated', false)
            ->assertJsonPath('data.user', null)
            ->assertJsonPath('data.expires_at', null)
            ->assertJsonPath('error', null);
    }

    public function test_session_returns_user_and_expiry_when_authenticated(): void
    {
        $user = User::factory()->create([
            'name' => 'Quan',
            'email' => 'quan@example.com',
        ]);

        Passport::actingAs($user, ['profile:read']);

        $response = $this->getJson('/api/v1/oauth/session');

        $response
            ->assertOk()
            ->assertJsonPath('data.authenticated', true)
            ->assertJsonPath('data.user.id', $user->getKey())
            ->assertJsonPath('data.user.name', 'Quan')
            ->assertJsonPath('data.user.email', 'quan@example.com')
            ->assertJsonPath('error', null);
    }
}
