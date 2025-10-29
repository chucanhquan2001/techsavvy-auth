<?php

namespace App\Providers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthenticateUserService;
use App\Infrastructure\OAuth\PassportTokenService;
use App\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AuthenticateUserService::class, PassportTokenService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
