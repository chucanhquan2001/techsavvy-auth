<?php

namespace App\Domain\Services;

use App\Domain\Entities\User;

interface AuthenticateUserService
{
    public function generateAccessToken(User $user): array;
}