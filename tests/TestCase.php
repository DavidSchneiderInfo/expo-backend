<?php

namespace Tests;

use App\Models\Profile;
use App\Repositories\MatchRepository;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertMatchRepoCounts(int $count, Profile $profile, string $message = ''): void
    {
        $repo = MatchRepository::forProfile($profile);
        $query = $repo->build();

        $this->assertEquals(
            $count,
            $query->count(),
            $message !== '' ? $message : 'Match repo has '. $query->count().' entries, expected where '.$count
        );
    }

    protected function getRepo(Profile $profile): MatchRepository
    {
        return MatchRepository::forProfile($profile);
    }
}
