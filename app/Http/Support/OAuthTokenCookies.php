<?php

namespace App\Http\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class OAuthTokenCookies
{
    public static function attach(Response $response, array $tokens): Response
    {
        $path = config('oauth_tokens.path', '/');
        $domain = config('oauth_tokens.domain');
        /** @var bool|null $secure null = infer from request (HTTPS) per Symfony Cookie */
        $secure = config('oauth_tokens.secure');
        $sameSite = self::sameSite(config('oauth_tokens.same_site', 'lax'));
        $partitioned = (bool) config('oauth_tokens.partitioned', false);

        $expiresIn = (int) ($tokens['expires_in'] ?? 0);
        $accessExpiresAt = $expiresIn > 0
            ? Carbon::now()->addSeconds($expiresIn)
            : Carbon::now()->addHour();

        $response->headers->setCookie(self::make(
            name: config('oauth_tokens.access_cookie'),
            value: $tokens['access_token'],
            expiresAt: $accessExpiresAt,
            path: $path,
            domain: $domain,
            secure: $secure,
            sameSite: $sameSite,
            partitioned: $partitioned,
        ));

        if (! empty($tokens['refresh_token'])) {
            $response->headers->setCookie(self::make(
                name: config('oauth_tokens.refresh_cookie'),
                value: $tokens['refresh_token'],
                expiresAt: Carbon::now()->addDays(30),
                path: $path,
                domain: $domain,
                secure: $secure,
                sameSite: $sameSite,
                partitioned: $partitioned,
            ));
        }

        return $response;
    }

    public static function forget(Response $response): Response
    {
        $path = config('oauth_tokens.path', '/');
        $domain = config('oauth_tokens.domain');
        /** @var bool|null $secure null = infer from request (HTTPS) per Symfony Cookie */
        $secure = config('oauth_tokens.secure');
        $sameSite = self::sameSite(config('oauth_tokens.same_site', 'lax'));
        $partitioned = (bool) config('oauth_tokens.partitioned', false);
        $expired = Carbon::createFromTimestampUTC(0);

        foreach ([config('oauth_tokens.access_cookie'), config('oauth_tokens.refresh_cookie')] as $name) {
            $response->headers->setCookie(self::make(
                name: $name,
                value: '',
                expiresAt: $expired,
                path: $path,
                domain: $domain,
                secure: $secure,
                sameSite: $sameSite,
                partitioned: $partitioned,
            ));
        }

        return $response;
    }

    private static function make(
        string $name,
        string $value,
        DateTimeInterface $expiresAt,
        string $path,
        ?string $domain,
        ?bool $secure,
        string $sameSite,
        bool $partitioned,
    ): Cookie {
        return Cookie::create($name, $value, $expiresAt, $path, $domain, $secure, true, false, $sameSite, $partitioned);
    }

    private static function sameSite(string $value): string
    {
        $v = strtolower($value);

        return match ($v) {
            'none' => Cookie::SAMESITE_NONE,
            'strict' => Cookie::SAMESITE_STRICT,
            default => Cookie::SAMESITE_LAX,
        };
    }
}
