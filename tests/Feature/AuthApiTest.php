<?php

namespace Tests\Feature;

use App\Application\Exceptions\OAuthTokenException;
use App\Application\Services\OAuthTokenService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_user_and_tokens(): void
    {
        $this->mock(OAuthTokenService::class, function (Mockery\MockInterface $mock): void {
            $mock->shouldReceive('issuePasswordToken')
                ->once()
                ->andReturn([
                    'access_token' => 'access-token',
                    'refresh_token' => 'refresh-token',
                    'token_type' => 'Bearer',
                    'expires_in' => 7200,
                    'scope' => 'profile:read',
                ]);
        });

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Quan',
            'email' => 'quan@example.com',
            'password' => 'secret123',
            'client_id' => 'client-1',
            'client_secret' => 'secret-1',
            'scope' => 'profile:read',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user.email', 'quan@example.com')
            ->assertJsonPath('data.tokens.access_token', 'access-token')
            ->assertJsonPath('error', null);

        $this->assertDatabaseHas('users', [
            'email' => 'quan@example.com',
        ]);
    }

    public function test_login_returns_oauth_error_shape(): void
    {
        User::factory()->create([
            'email' => 'quan@example.com',
        ]);

        $this->mock(OAuthTokenService::class, function (Mockery\MockInterface $mock): void {
            $mock->shouldReceive('issuePasswordToken')
                ->once()
                ->andThrow(new OAuthTokenException('Invalid credentials or refresh token.', 400, 'invalid_grant'));
        });

        $response = $this->postJson('/api/v1/auth/login/password', [
            'email' => 'quan@example.com',
            'password' => 'wrong-password',
            'client_id' => 'client-1',
            'client_secret' => 'secret-1',
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonPath('error.code', 'invalid_grant')
            ->assertJsonPath('error.message', 'Invalid credentials or refresh token.');
    }

    public function test_me_returns_authenticated_user_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'quan@example.com',
        ]);

        Passport::actingAs($user, ['profile:read']);

        $response = $this->getJson('/api/v1/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.email', 'quan@example.com')
            ->assertJsonPath('error', null);
    }
}
