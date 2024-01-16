<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\Http\Resources\MatchResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class MatchController
{
    public function __invoke(Request $request): MatchResource
    {
        /** @var Profile $givingUser */
        $givingUser = $request->user()->profile;

        $request->validate([
            'user_id' => 'required|exists:profiles,id',
        ]);

        /** @var User $receivingUser */
        $userId = (int)$request->get('user_id');
        $receivingUser = Profile::query()->findOrFail($userId);

        $givingUser->likesToUsers()->save($receivingUser);

        return new MatchResource($givingUser->likesFromUsers()->where('user_id', $userId)->count() > 0);
    }
}
