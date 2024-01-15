<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Actions\CreateToken;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulAuthenticationResource;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(
        private readonly CreateToken $createToken,
        private readonly Guard $guard
    )
    {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse|SuccessfulAuthenticationResource
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        /** @var User $user */
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if($user === null || $this->guard->validate($credentials) !== true)
        {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $this->createToken->getTokenForUser($user);

        return new SuccessfulAuthenticationResource($user, $token);
    }
}
