<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Photo;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUsers();

        /** @var User $me */
        $me = User::firstOrCreate([
            'email' => 'd4vid81@gmail.com',
        ], [
            'password' => bcrypt('secret12'),
            'email_verified_at' => Carbon::now(),
        ]);

        $me->profile()->save(new Profile([
            'name' => 'David',
            'bio' => 'Some cool dude doing crazy tech stuff',
            'birthday' => '1981-10-20',
        ]));

        $this->createUsers($me);
    }

    public function createUsers(?User $me = null): void
    {

        Profile::factory(100)->create()->each(function (Profile $profile) use ($me) {
            if($me)
                $profile->user->likesToUsers()->save($me);
        });
    }
}
