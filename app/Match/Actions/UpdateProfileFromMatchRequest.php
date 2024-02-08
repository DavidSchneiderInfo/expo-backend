<?php

declare(strict_types=1);

namespace App\Match\Actions;

use App\Http\Requests\Match\MatchRequest;
use App\Models\Profile;
use App\Models\User;

class UpdateProfileFromMatchRequest
{
    public function update(MatchRequest $request): Profile
    {
        $profile = $this->getProfile($request->user());

        $profile->update([
            'latitude' => $request->getLatitude(),
            'longitude' => $request->getLatitude(),
        ]);

        return $profile;
    }

    private function getProfile(User $user): Profile
    {
        return $user->profile;
    }
}
