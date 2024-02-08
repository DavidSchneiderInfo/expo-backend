<?php

namespace App\Http\Controllers\Match;

use App\Http\Controllers\Controller;
use App\Http\Requests\Match\MatchRequest;
use App\Http\Resources\ProfileResource;
use App\Match\Actions\UpdateProfileFromMatchRequest;
use App\Models\Profile;
use App\Repositories\MatchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetMatchListController extends Controller
{
    public function __construct(
        private readonly UpdateProfileFromMatchRequest $profileUpdate
    ) {}

    public function __invoke(MatchRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $profile = $this->profileUpdate->update($request);

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
