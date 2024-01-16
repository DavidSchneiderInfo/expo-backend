<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ProfileException;
use App\Http\Controllers\Controller;
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
    public function __invoke(Request $request): ProfileResource
    {
        $requestData = $request->validate([
            'name' => 'sometimes|string|between:1,20',
            'bio' => 'sometimes|string|between:1,500',
            'sex' => 'sometimes|in:f,m,x',
            'height' => 'sometimes|integer|between:60,240',
        ]);

        /** @var User $user */
        $user = $request->user();

        return new ProfileResource($this->updateProfile->update($user, $requestData));
    }
}
