<?php

namespace App\Application\Services;

use App\Application\UseCases\LoginUseCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(private LoginUseCase $useCase) {}

    public function login(string $email, string $password): array
    {
        return DB::transaction(function () use ($email, $password) {
            $result = $this->useCase->execute($email, $password);
            Log::info('User logged in', ['email' => $email]);
            return $result;
        });
    }

    public function register(string $name, string $email, string $password): array
    {
        return DB::transaction(function () use ($name, $email, $password) {
            $registerUseCase = app(\App\Application\UseCases\RegisterUseCase::class);
            $result = $registerUseCase->execute($name, $email, $password);
            Log::info('New user registered', ['email' => $email]);
            return $result;
        });
    }
}
