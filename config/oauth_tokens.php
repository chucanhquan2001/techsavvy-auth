<?php

return [

    'access_cookie' => env('OAUTH_ACCESS_TOKEN_COOKIE', 'oauth_access_token'),

    'refresh_cookie' => env('OAUTH_REFRESH_TOKEN_COOKIE', 'oauth_refresh_token'),

    'path' => env('OAUTH_TOKEN_COOKIE_PATH', '/'),

    'domain' => env('OAUTH_TOKEN_COOKIE_DOMAIN'),

    /*
     * false = cookie works on http://localhost (dev). Production: set OAUTH_TOKEN_COOKIE_SECURE=true.
     * null = let Symfony infer from the request (HTTPS → Secure cookie).
     */
    'secure' => env('OAUTH_TOKEN_COOKIE_SECURE') === null
        ? null
        : filter_var(env('OAUTH_TOKEN_COOKIE_SECURE'), FILTER_VALIDATE_BOOLEAN),

    'same_site' => env('OAUTH_TOKEN_COOKIE_SAMESITE', 'lax'),

    'partitioned' => env('OAUTH_TOKEN_COOKIE_PARTITIONED', false),

];
