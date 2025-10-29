<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Models\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        $model = EloquentUser::where('email', $email)->first();
        return $model ? new User($model->id, $model->email, $model->name) : null;
    }

    public function verifyPassword(User $user, string $password): bool
    {
        $model = EloquentUser::find($user->id);
        return $model && Hash::check($password, $model->password);
    }
}