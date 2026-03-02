<?php

namespace App\Repositories;

use App\Models\Token;

class TokenRepository
{
    public function findByTokenAndUserId(string $token, string $userId): ?Token
    {
        return Token::where('token', $token)
                    ->where('user_id', $userId)
                    ->first();
    }

    public function deleteByUserId(string $userId): void
    {
        Token::where('user_id', $userId)->delete();
    }
}
