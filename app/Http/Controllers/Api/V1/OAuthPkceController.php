<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Exceptions\OAuthTokenException;
use App\Application\Services\OAuthTokenService;
use App\Application\Support\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OAuth\PkceTokenExchangeRequest;
use App\Http\Support\OAuthTokenCookies;
use Illuminate\Http\JsonResponse;

class OAuthPkceController extends Controller
{
    public function __construct(private readonly OAuthTokenService $tokenService) {}

    /**
     * Exchange authorization code (PKCE) and return tokens only as HttpOnly cookies.
     */
    public function exchange(PkceTokenExchangeRequest $request): JsonResponse
    {
        try {
            $tokens = $this->tokenService->issueAuthorizationCodeToken([
                'client_id' => $request->string('client_id')->toString(),
                'client_secret' => $request->input('client_secret'),
                'redirect_uri' => $request->string('redirect_uri')->toString(),
                'code' => $request->string('code')->toString(),
                'code_verifier' => $request->string('code_verifier')->toString(),
                'scope' => $request->input('scope', ''),
            ]);
        } catch (OAuthTokenException $exception) {
            return ApiResponse::error(
                $exception->getMessage(),
                $exception->status(),
                $exception->oauthError(),
                $exception->context(),
            );
        }

        $json = ApiResponse::success([
            'token_type' => $tokens['token_type'],
            'expires_in' => $tokens['expires_in'],
            'scope' => $tokens['scope'],
        ]);

        return OAuthTokenCookies::attach($json, $tokens);
    }
}
