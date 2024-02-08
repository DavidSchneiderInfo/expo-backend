<?php

namespace Database\Seeders;

use App\Enums\Sex;
use App\Match\ValueObjects\SearchRadius;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\MatchRepository;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var User $me */
        $me = User::query()
            ->firstOrCreate([
                'email' => 'd4vid81@gmail.com',
            ], [
                'password' => bcrypt('secret12'),
                'email_verified_at' => Carbon::now(),
            ]);

        $me->profile()->save(new Profile([
            'name' => 'David',
            'bio' => 'Some cool dude doing crazy tech stuff',
            'birthday' => '1981-10-20',
            'sex' => Sex::m,
            'height' => 182,
            'i_f' => true,
            'i_m' => false,
            'i_x' => false,
            'latitude' => 37.785834,
            'longitude' => -122.406417,
            'maxDistance' => null,
        ]));

        $existing = Profile::query()->whereNot('id', $me->profile->id)->count();
        if($existing<1000)
        {
            Profile::factory()
                ->count(1000-$existing)
                ->create();
        }

        $existingInterested = MatchRepository::forProfile($me->profile)->getProfiles()->count();
        if($existingInterested<100)
        {
            Profile::factory()
                ->interestedInProfilesLike($me->profile)
                ->withinSearchRadius(SearchRadius::forProfile($me->profile))
                ->count(100-$existingInterested)
                ->create();
        }

        $existingMatches = $me->profile->matches()->count();
        if($existingMatches<25)
        {
            $profiles = MatchRepository::forProfile($me->profile)
                ->getProfiles()
                ->limit(25)
                ->get();
            $me->profile->likesFromUsers()->sync($profiles);
        }
    }

    public function createUsers(?User $me = null): void
    {
        $factory = Profile::factory(100);

        if($me)
        {
            $searchRadius = new SearchRadius(
                $me->profile->latitude,
                $me->profile->longitude,
                $me->profile->maxDistance ?? 0
            );

            $factory = $factory->withinCoordinates(
                $searchRadius->latitudeMin,
                $searchRadius->latitudeMax,
                $searchRadius->longitudeMin,
                $searchRadius->longitudeMax
            );
        }
        $factory->create()
            ->each(function (Profile $profile) use ($me) {
                if($me)
                    $profile->likesToUsers()->save($me->profile);
            });
    }
}
