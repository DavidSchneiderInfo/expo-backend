<?php

namespace Tests;

use App\Models\Profile;
use App\Repositories\MatchRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Tests\Traits\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertMatchRepoCounts(int $count, Profile $profile, string $message = ''): void
    {
        $query = MatchRepository::forProfile($profile)
            ->getProfiles();

        $this->assertEquals(
            $count,
            $query->count(),
            $message !== '' ? $message : 'Match repo has '. $query->count().' within distance, expected where '.$count
        );
    }

    protected function getRepo(Profile $profile): MatchRepository
    {
        return MatchRepository::forProfile($profile);
    }

    private function dd(Builder $query, int $count, Profile $profile, string $message = ''): void
    {
        if($count!=$query->count())
        {
            $data = [
                'wanted' => [
                    'sex' => $profile->sex,
                    'i_f' => $profile->i_f,
                    'i_m' => $profile->i_m,
                    'i_x' => $profile->i_x,
                ],
                'available' => DB::table('profiles')->select([
                        'sex',
                        'i_f',
                        'i_m',
                        'i_x'
                    ])
                    ->get()
                    ->toArray(),
                'sql' => $query->toRawSql()
            ];;

            dd(
                $data,
                $message
            );
        }
    }
}
