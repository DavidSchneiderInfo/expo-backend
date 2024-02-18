<?php

declare(strict_types=1);

namespace App\Auth\Actions;

use App\Auth\Traits\PreparesTokens;
use App\Auth\ValueObjects\Token;
use App\Models\User;

class CreateToken {
    use PreparesTokens;

    public function getTokenForUser(User $user): Token
    {
        return $this->prepareToken($user);
    }
}
