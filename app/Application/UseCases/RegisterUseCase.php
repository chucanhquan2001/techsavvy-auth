<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthenticateUserService;
use Exception;

class RegisterUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AuthenticateUserService $auth
    ) {}

    public function execute(string $name, string $email, string $password): array
    {
        if ($this->users->findByEmail($email)) {
            throw new Exception('Email already exists');
        }

        $user = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return $this->auth->generateAccessToken($user);
    }
}