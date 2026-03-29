<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Passport::viewPrefix('oauth');

        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addHours(2));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::tokensCan([
            'openid' => 'OpenID Connect (subject identifier).',
            'profile' => 'End-user profile (name, etc.).',
            'email' => 'Email address.',
            'profile:read' => 'Read the authenticated user profile.',
            'profile:write' => 'Update the authenticated user profile.',
        ]);
        Passport::setDefaultScope([
            'profile:read',
        ]);
    }
}
