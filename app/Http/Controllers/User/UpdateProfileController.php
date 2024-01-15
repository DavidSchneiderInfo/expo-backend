<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ProfileException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Profile\Actions\UpdateProfile;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    public function __construct(
        private readonly UpdateProfile $updateProfile
    ) {}

    /**
     * @throws ProfileException
     */
    public function __invoke(Request $request): UserResource
    {
        $requestData = $request->validate([
            'birthday' => 'required',
            'name' => 'required',
            'bio' => 'required',
        ]);

        /** @var User $user */
        $user = $request->user();

        $this->updateProfile->update($user, $requestData);

        return new UserResource($user);
    }
}
