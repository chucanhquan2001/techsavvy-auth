<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthenticateUserService;
use Exception;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private AuthenticateUserService $auth
    ) {}

    public function execute(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !$this->users->verifyPassword($user, $password)) {
            throw new Exception('Invalid credentials');
        }

        return $this->auth->generateAccessToken($user);
    }
}
