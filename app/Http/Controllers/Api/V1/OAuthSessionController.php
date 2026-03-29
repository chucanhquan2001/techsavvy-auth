<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Support\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\AccessToken;
use Laravel\Passport\Token;

class OAuthSessionController extends Controller
{
    /**
     * Inspect current access token (cookie or Bearer): validity, user, expiry.
     */
    public function __invoke(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return ApiResponse::success([
                'authenticated' => false,
                'user' => null,
                'expires_at' => null,
            ]);
        }

        $accessToken = $user->token();
        $expiresAt = match (true) {
            $accessToken instanceof Token => $accessToken->expires_at?->toIso8601ZuluString(),
            $accessToken instanceof AccessToken => $accessToken->expires_at?->toIso8601ZuluString(),
            default => null,
        };

        return ApiResponse::success([
            'authenticated' => true,
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ],
            'expires_at' => $expiresAt,
        ]);
    }
}
