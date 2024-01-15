<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController
{
    public function __invoke(Request $request): JsonResponse
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

        return response()->json([
            'match' => $givingUser->likesFromUsers()->where('user_id', $userId)->count() > 0
        ]);
    }
}
