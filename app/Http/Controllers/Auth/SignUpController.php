<?php

namespace App\Http\Controllers\Auth;

use App\Auth\Actions\CreateToken;
use App\Exceptions\ProfileException;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulAuthenticationResource;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class SignUpController extends Controller
{
    public function __construct(
        private readonly CreateToken $createToken,
        private readonly CreateProfile $createProfile,
        private readonly LoggerInterface $logger
    )
    {}

    public function __invoke(Request $request): JsonResponse|SuccessfulAuthenticationResource
    {
        $request->validate([
            'username' => 'required|string|between:6,32',
            'email' => 'required|email',
            'password' => 'required',
            'birthday' => 'required|date',
        ]);

        try {
            $user = new User([
                'password' => bcrypt($request->get('password')),
                'email' => $request->get('email'),
            ]);
            $user->save();

            $this->createProfile->create($user, [
                'name' => $request->get('username'),
                'birthday' => $request->get('birthday'),
            ]);

            $token = $this->createToken->getTokenForUser($user);

            return new SuccessfulAuthenticationResource($user, $token);
        }
        catch (UniqueConstraintViolationException)
        {
            return response()->json([
                'message' => 'These credentials have already been taken.',
            ], 403);
        } catch (ProfileException $e) {
            $this->logger->error($e->getMessage(), [
                'user_id'
            ]);

            return response()->json([
                'message' => 'The profile could not be created.',
            ], 403);
        }
    }
}
