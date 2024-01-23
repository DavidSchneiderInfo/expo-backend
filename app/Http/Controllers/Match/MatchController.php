<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\Http\Requests\Match\MatchRequest;
use App\Http\Resources\MatchResource;
use App\Models\Profile;
use Exception;
use Illuminate\Support\Facades\Log;

class MatchController
{
    public function __invoke(MatchRequest $request): MatchResource
    {
        /** @var Profile $givingUser */
        $givingUser = $request->user()->profile;

        $request->validate([
            'user_id' => 'required|exists:profiles,id',
        ]);

        try
        {
            /** @var Profile $receivingUser */
            $receivingUser = Profile::query()->findOrFail($request->get('user_id'));
            $givingUser->likesToUsers()->save($receivingUser);
            return new MatchResource($givingUser->likesFromUsers()->where('user_id', $receivingUser->user_id)->count() > 0);

        }
        catch (Exception $e)
        {
            Log::error('Match error', [
                'exception' => $e,
            ]);
            return new MatchResource(false);
        }
    }
}
