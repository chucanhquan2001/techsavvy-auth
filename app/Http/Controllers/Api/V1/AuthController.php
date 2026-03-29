<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Exceptions\EmailAlreadyExistsException;
use App\Application\Exceptions\OAuthTokenException;
use App\Application\Services\AuthServerService;
use App\Application\Support\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\PasswordLoginRequest;
use App\Http\Requests\Api\V1\Auth\RefreshTokenRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Support\OAuthTokenCookies;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthServerService $authServer) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authServer->register(
                name: $request->string('name')->toString(),
                email: $request->string('email')->toString(),
                password: $request->string('password')->toString(),
                clientId: $request->string('client_id')->toString(),
                clientSecret: $request->input('client_secret'),
                scope: $request->input('scope', ''),
            );

            return ApiResponse::success($result, status: 201);
        } catch (EmailAlreadyExistsException $exception) {
            return ApiResponse::error($exception->getMessage(), 422, 'email_exists');
        } catch (OAuthTokenException $exception) {
            return ApiResponse::error(
                $exception->getMessage(),
                $exception->status(),
                $exception->oauthError(),
                $exception->context(),
            );
        }
    }

    public function loginWithPassword(PasswordLoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authServer->loginWithPassword(
                email: $request->string('email')->toString(),
                password: $request->string('password')->toString(),
                clientId: $request->string('client_id')->toString(),
                clientSecret: $request->input('client_secret'),
                scope: $request->input('scope', ''),
            );

            return ApiResponse::success($result);
        } catch (ModelNotFoundException) {
            return ApiResponse::error('User account not found.', 404, 'user_not_found');
        } catch (OAuthTokenException $exception) {
            return ApiResponse::error(
                $exception->getMessage(),
                $exception->status(),
                $exception->oauthError(),
                $exception->context(),
            );
        }
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $tokens = $this->authServer->refreshToken(
                refreshToken: $request->string('refresh_token')->toString(),
                clientId: $request->string('client_id')->toString(),
                clientSecret: $request->input('client_secret'),
                scope: $request->input('scope', ''),
            );

            return ApiResponse::success($tokens);
        } catch (OAuthTokenException $exception) {
            return ApiResponse::error(
                $exception->getMessage(),
                $exception->status(),
                $exception->oauthError(),
                $exception->context(),
            );
        }
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success($this->authServer->me($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authServer->revokeCurrentToken($request->user());

        $response = ApiResponse::success([
            'revoked' => true,
        ]);

        return OAuthTokenCookies::forget($response);
    }
}
