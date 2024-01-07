<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Photo;
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

        $me = User::firstOrCreate([
            'email' => 'd4vid81@gmail.com',
        ], [
            'name' => 'David',
            'bio' => 'Some cool dude doing crazy tech stuff',
            'password' => bcrypt('secret12'),
            'birthday' => '1981-10-20',
            'email_verified_at' => Carbon::now(),
        ]);

        $this->createUsers($me);
    }

    public function createUsers(?User $me = null): void
    {

        User::factory(100)->create()->each(function (User $user) use ($me) {
            if($me)
                $user->likesToUsers()->save($me);
        });
    }
}
