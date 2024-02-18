<?php

declare(strict_types=1);

namespace App\Auth\Actions;

use App\Auth\Traits\PreparesTokens;
use App\Auth\ValueObjects\Token;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class RefreshToken {
    use PreparesTokens;

    /**
     * @throws Exception
     */
    public function getTokenForRequest(Request $request): Token
    {
        $user = $request->user();
        if($user === null)
        {
            throw new Exception("Refreshing session without logged in user.");
        }

        /** @var PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        if($token->expires_at && $token->expires_at->diff(Carbon::now())->days>1)
        {
            return new Token(
                explode(" ", $request->header('Authorization'))[1],
                $token->expires_at
            );
        }

        $token->delete();
        return $this->prepareToken($user);
    }
}
