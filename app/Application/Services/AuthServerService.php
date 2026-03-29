<?php

namespace App\Application\Services;

use App\Application\Exceptions\EmailAlreadyExistsException;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Hash;

class AuthServerService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly OAuthTokenService $tokenService,
        private readonly DatabaseManager $database,
    ) {}

    public function register(
        string $name,
        string $email,
        string $password,
        string $clientId,
        ?string $clientSecret,
        string $scope = ''
    ): array {
        return $this->database->transaction(function () use ($name, $email, $password, $clientId, $clientSecret, $scope) {
            if ($this->users->findByEmail($email)) {
                throw new EmailAlreadyExistsException('Email already exists.');
            }

            $user = UserModel::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            return [
                'user' => $this->mapUser($user),
                'tokens' => $this->tokenService->issuePasswordToken(
                    email: $email,
                    password: $password,
                    clientId: $clientId,
                    clientSecret: $clientSecret,
                    scope: $scope,
                ),
            ];
        });
    }

    public function loginWithPassword(
        string $email,
        string $password,
        string $clientId,
        ?string $clientSecret,
        string $scope = ''
    ): array {
        $tokens = $this->tokenService->issuePasswordToken(
            email: $email,
            password: $password,
            clientId: $clientId,
            clientSecret: $clientSecret,
            scope: $scope,
        );

        $user = UserModel::query()
            ->where('email', $email)
            ->firstOrFail();

        return [
            'user' => $this->mapUser($user),
            'tokens' => $tokens,
        ];
    }

    public function refreshToken(
        string $refreshToken,
        string $clientId,
        ?string $clientSecret,
        string $scope = ''
    ): array {
        return $this->tokenService->issueRefreshToken(
            refreshToken: $refreshToken,
            clientId: $clientId,
            clientSecret: $clientSecret,
            scope: $scope,
        );
    }

    public function revokeCurrentToken(UserModel $user): void
    {
        $accessToken = $user->token();

        if (! $accessToken) {
            return;
        }

        $this->tokenService->revokeAccessTokenWithRefreshTokens($accessToken->id);
    }

    public function me(UserModel $user): array
    {
        return $this->mapUser($user);
    }

    private function mapUser(UserModel $user): array
    {
        return [
            'id' => $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toAtomString(),
            'created_at' => $user->created_at?->toAtomString(),
            'updated_at' => $user->updated_at?->toAtomString(),
        ];
    }
}
