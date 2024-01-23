<?php

declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Enums\Sex;
use App\Models\Profile;
use App\Repositories\MatchRepository;
use Tests\RefreshDatabaseFast;
use Tests\TestCase;

class MatchRepositoryTest extends TestCase
{
    use RefreshDatabaseFast;

    public function testRepoCanShowEmptyLists(): void
    {
        $profile = Profile::factory()->create();
        $repo = $this->getRepo($profile);
        $this->assertEquals(0, $repo->getProfiles()->count());
    }

    public static function provideLikableScenarios(): array
    {
        return [
            'man liking women' => [
                [
                    'sex' => Sex::m,
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                ],
            ],
            'woman liking men' => [
                [
                    'sex' => Sex::f,
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => false,
                ],
            ],
            'other liking men' => [
                [
                    'sex' => Sex::x,
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => false,
                ],
            ],
            'other liking women' => [
                [
                    'sex' => Sex::x,
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLikableScenarios
     */
    public function testRepoCanShowAResult(array $attributes): void
    {
        $profile = Profile::factory()
            ->create($attributes);

        Profile::factory(5)
            ->interestedInProfilesLike($profile)
            ->create();

        Profile::factory(5)
            ->notInterestedInProfilesLike($profile)
            ->create();

        $this->assertEquals(10, $this->getRepo($profile)->build()->count());
        $this->assertEquals(5, $this->getRepo($profile)->filterGenders()->build()->count());
    }

    private function getRepo(Profile $profile): MatchRepository
    {
        return MatchRepository::forProfile($profile);
    }
}
