<?php

namespace App\Auth\Traits;

use App\Auth\ValueObjects\Token;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

trait PreparesTokens
{
    public function prepareToken(User $user): Token
    {
        /** @var NewAccessToken $token */
        $token = $user->createToken('mobile', ['*'], now()->addWeek());
        return new Token(
            $token->plainTextToken,
            $token->accessToken->expires_at
        );
    }
}
