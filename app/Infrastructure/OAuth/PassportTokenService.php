<?php

namespace App\Infrastructure\OAuth;

use App\Domain\Services\AuthenticateUserService;
use App\Domain\Entities\User;
use App\Models\User as EloquentUser;

class PassportTokenService implements AuthenticateUserService
{
    public function generateAccessToken(User $user): array
    {
        $model = EloquentUser::find($user->id);
        $token = $model->createToken('auth-server');
        return [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => $token->token->expires_at?->timestamp,
        ];
    }
}