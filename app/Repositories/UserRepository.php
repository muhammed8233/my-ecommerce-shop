<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository 
{
    public function findByEmail(string $email): ?User
    {
        // Equivalent to Optional<User>
        return User::where('email', $email)->first();
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}
