<?php

namespace App\Application\Services;

use App\Application\Exceptions\OAuthTokenException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class OAuthTokenService
{
    public function __construct(private readonly Kernel $kernel) {}

    public function issuePasswordToken(
        string $email,
        string $password,
        string $clientId,
        ?string $clientSecret,
        string $scope = ''
    ): array {
        return $this->issueToken([
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => $scope,
        ]);
    }

    public function issueRefreshToken(
        string $refreshToken,
        string $clientId,
        ?string $clientSecret,
        string $scope = ''
    ): array {
        return $this->issueToken([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => $scope,
        ]);
    }

    /**
     * Authorization Code + PKCE (public or confidential client).
     *
     * @param  array{grant_type: string, client_id: string, redirect_uri: string, code: string, code_verifier: string, client_secret?: string|null, scope?: string}  $params
     */
    public function issueAuthorizationCodeToken(array $params): array
    {
        return $this->issueToken([
            'grant_type' => 'authorization_code',
            'client_id' => $params['client_id'],
            'client_secret' => $params['client_secret'] ?? null,
            'redirect_uri' => $params['redirect_uri'],
            'code' => $params['code'],
            'code_verifier' => $params['code_verifier'],
            'scope' => $params['scope'] ?? '',
        ]);
    }

    public function revokeAccessTokenWithRefreshTokens(string $accessTokenId): void
    {
        /** @var Token|null $accessToken */
        $accessToken = Token::query()->find($accessTokenId);

        if (! $accessToken) {
            return;
        }

        $accessToken->revoke();

        RefreshToken::query()
            ->where('access_token_id', $accessTokenId)
            ->update(['revoked' => true]);
    }

    private function issueToken(array $payload): array
    {
        $request = Request::create('/oauth/token', 'POST', Arr::where($payload, fn ($value) => $value !== null && $value !== ''));
        $request->headers->set('Accept', 'application/json');

        $response = $this->kernel->handle($request);
        $this->kernel->terminate($request, $response);

        if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST) {
            $this->throwTokenException($response);
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return [
            'access_token' => $decoded['access_token'],
            'refresh_token' => $decoded['refresh_token'] ?? null,
            'token_type' => $decoded['token_type'] ?? 'Bearer',
            'expires_in' => $decoded['expires_in'] ?? null,
            'scope' => $decoded['scope'] ?? null,
        ];
    }

    private function throwTokenException(Response $response): never
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($response->getContent(), true) ?: [];

        $message = $decoded['message'] ?? 'Unable to issue OAuth token.';
        $error = $decoded['error'] ?? null;

        if ($error === 'invalid_grant') {
            $message = 'Invalid credentials or refresh token.';
        }

        throw new OAuthTokenException(
            message: $message,
            status: $response->getStatusCode(),
            oauthError: is_string($error) ? $error : null,
            context: Arr::only($decoded, ['hint'])
        );
    }
}
