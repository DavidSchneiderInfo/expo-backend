<?php

declare(strict_types=1);

namespace App\Auth\Actions;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class CreateToken {
    public function getTokenForUser(User $user): NewAccessToken
    {
        return $user->createToken('mobile', ['*'], now()->addWeek());
    }
}
