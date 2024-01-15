<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Actions\CreateToken;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulAuthenticationResource;
use Exception;
use Illuminate\Http\Request;

class RefreshSessionController extends Controller
{
    public function __construct(
        private readonly CreateToken $createToken
    ){}

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): SuccessfulAuthenticationResource
    {
        $user = $request->user();
        if($user === null)
        {
            throw new Exception("Refreshing session without logged in user.");
        }

        $token = $this->createToken->getTokenForUser($user);

        return new SuccessfulAuthenticationResource($user, $token);
    }
}
