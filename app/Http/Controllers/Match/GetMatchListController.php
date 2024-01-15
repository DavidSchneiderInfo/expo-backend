<?php

namespace App\Http\Controllers\Match;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Repositories\MatchRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetMatchListController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        /** @var Profile $profile */
        $profile = $request->user()->profile;

        if($profile->matches()->count()==0) {
            return ProfileResource::collection(
                MatchRepository::forProfile($profile)
                    ->getProfiles()
                    ->limit(50)
                    ->get()
            );
        } else {
            return ProfileResource::collection(
                $profile->matches()
                    ->limit(1)
                    ->get()
            );
        }
    }
}
