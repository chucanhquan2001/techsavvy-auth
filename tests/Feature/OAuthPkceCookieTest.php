<?php

namespace Tests\Feature;

use App\Application\Services\OAuthTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OAuthPkceCookieTest extends TestCase
{
    use RefreshDatabase;

    public function test_pkce_exchange_sets_http_only_cookies_and_omits_tokens_from_json(): void
    {
        $this->mock(OAuthTokenService::class, function (Mockery\MockInterface $mock): void {
            $mock->shouldReceive('issueAuthorizationCodeToken')
                ->once()
                ->andReturn([
                    'access_token' => 'access-secret',
                    'refresh_token' => 'refresh-secret',
                    'token_type' => 'Bearer',
                    'expires_in' => 7200,
                    'scope' => 'profile:read',
                ]);
        });

        $response = $this->postJson('/api/v1/oauth/pkce/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'pub-client',
            'redirect_uri' => 'https://app.example.com/callback',
            'code' => 'auth-code',
            'code_verifier' => 'verifier',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.expires_in', 7200)
            ->assertJsonMissingPath('data.access_token');

        $response->assertCookie(config('oauth_tokens.access_cookie'), 'access-secret', false);
        $response->assertCookie(config('oauth_tokens.refresh_cookie'), 'refresh-secret', false);

        $accessCookie = $response->headers->getCookies()[0] ?? null;
        $this->assertNotNull($accessCookie);
        $this->assertTrue($accessCookie->isHttpOnly());
    }
}
