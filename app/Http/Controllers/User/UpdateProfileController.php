<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ProfileException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
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
    public function __invoke(UpdateProfileRequest $request): ProfileResource
    {
        /** @var User $user */
        $user = $request->user();

        return new ProfileResource($this->updateProfile->update($user, $request->getAttributes()));
    }
}
