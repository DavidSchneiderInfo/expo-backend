<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Actions\RefreshToken;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulAuthenticationResource;
use Exception;
use Illuminate\Http\Request;

class RefreshSessionController extends Controller
{
    public function __construct(
        private readonly RefreshToken $refreshToken
    ){}

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): SuccessfulAuthenticationResource
    {
        $token = $this->refreshToken->getTokenForRequest($request);

        return new SuccessfulAuthenticationResource($request->user(), $token);
    }
}
