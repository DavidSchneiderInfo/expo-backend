<?php

namespace Database\Seeders;

use App\Enums\Sex;
use App\Match\ValueObjects\SearchRadius;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\MatchRepository;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->cleanUp();

        $me = $this->setupMe();

        $this->fillWithFaces();;

        //$this->fillUp($me);
    }

    private function fillWithFaces(): void
    {
        $c=1;
        foreach ([Sex::m, Sex::f] as $sex)
        for($x=1; $x<=5; $x++)
        {
            /** @var Profile $profile */
            $profile = Profile::factory()->create([
                'sex' => $sex,
                'i_f' => true,
                'i_m' => true,
                'i_x' => false,
            ]);

            $this->prepareMedia($profile->id);
            $this->copy(
                'examples/'.$sex.$x.'.jpg',
                'app/profiles/'.$profile->id.'/avatar.jpg'
            );
            $this->copy(
                'examples/c'.$c.'.jpg',
                'app/profiles/'.$profile->id.'/cover.jpg'
            );

            $profile->update([
                'avatar' => $profile->id . '/avatar.jpg',
                'cover' => $profile->id . '/cover.jpg',
            ]);

            $c++;
        }
    }

    private function fillUp(Profile $me): void
    {
        $existing = Profile::query()->whereNot('id', $me->id)->count();
        if($existing<1000)
        {
            Profile::factory()
                ->count(1000-$existing)
                ->create();
        }

        $existingInterested = MatchRepository::forProfile($me)->getProfiles()->count();
        if($existingInterested<100)
        {
            Profile::factory()
                ->interestedInProfilesLike($me)
                ->withinSearchRadius(SearchRadius::forProfile($me))
                ->count(100-$existingInterested)
                ->create();
        }

        $existingMatches = $me->matches()->count();
        if($existingMatches<25)
        {
            $profiles = MatchRepository::forProfile($me)
                ->getProfiles()
                ->limit(25)
                ->get();
            $me->likesFromUsers()->sync($profiles);
        }
    }

    public function cleanUp(): void
    {
        shell_exec('rm -rf '.storage_path('app/profiles'));
        User::query()->delete();
    }

    public function setupMe(): Profile
    {
        /** @var User $user */
        $user = User::query()
            ->firstOrCreate([
                'email' => 'd4vid81@gmail.com',
            ], [
                'password' => bcrypt('secret12'),
                'email_verified_at' => Carbon::now(),
            ]);

        $user->profile()->save(new Profile([
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
            'active' => true,
        ]));

        $me = $user->profile;

        $this->prepareMedia($me->id);
        $this->copy(
            'examples/avatar.jpeg',
            'app/profiles/'.$me->id.'/avatar.jpg'
        );
        $this->copy(
            'examples/cover.jpeg',
            'app/profiles/'.$me->id.'/cover.jpg'
        );

        $me->update([
            'avatar' => $me->id . '/avatar.jpg',
            'cover' => $me->id . '/cover.jpg',
        ]);

        return $me;
    }

    private function prepareMedia(string $id): void
    {
        mkdir(storage_path('app/profiles/'.$id), 0755, true);
    }

    private function copy(string $source, string $target)
    {
        copy(resource_path($source), storage_path($target));
    }
}
