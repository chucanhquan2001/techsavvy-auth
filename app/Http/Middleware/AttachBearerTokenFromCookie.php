<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachBearerTokenFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $name = config('oauth_tokens.access_cookie');
        $token = $name ? $request->cookie($name) : null;

        if (is_string($token) && $token !== '' && ! $request->bearerToken()) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
