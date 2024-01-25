<?php

declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Match\ValueObjects\SearchRadius;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ProvidesLikeableScenarios;

class MatchRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use ProvidesLikeableScenarios;

    public function testRepoCanShowEmptyLists(): void
    {
        $profile = Profile::factory()->create();
        $this->assertMatchRepoCounts(0, $profile);
    }

    public function testRepoOnlyShowsActiveProfiles(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Profile::factory()
            ->interestedInProfilesLike($profile)
            ->notActive()
            ->create();
        Profile::factory()
            ->interestedInProfilesLike($profile)
            ->create();

        $this->assertMatchRepoCounts(1,$profile);
    }

    /**
     * @dataProvider provideLikableScenarios
     */
    public function testRepoCanShowResults(array $attributes): void
    {
        $profile = Profile::factory()
            ->create($attributes);

        Profile::factory(5)
            ->interestedInProfilesLike($profile)
            ->create();

        Profile::factory(5)
            ->notInterestedInProfilesLike($profile)
            ->create();

        $this->assertMatchRepoCounts(10, $profile);
        $this->assertEquals(5, $this->getRepo($profile)->filterGenders()->build()->count());
    }

    /**
     * @dataProvider provideLocationScenarios
     */
    public function testRepoShowsResultsWithinRange(float $latitude, float $longitude, int $radius): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()
            ->withCoordinates($latitude, $longitude)
            ->create([
                'maxDistance' => $radius,
            ]);

        $searchRadius = new SearchRadius($latitude, $longitude, $radius);

        Profile::factory(10)
            ->interestedInProfilesLike($profile)
            ->withinSearchRadius($searchRadius)
            ->create();

        Profile::factory(10)
            ->interestedInProfilesLike($profile)
            ->outsideSearchRadius($searchRadius)
            ->create();

        $this->assertMatchRepoCounts(20, $profile);
        $this->assertEquals(10, $this->getRepo($profile)->filterDistance()->build()->count());
    }

    public static function provideLocationScenarios(): array
    {
        return [
            'basic' => [
                fake()->latitude,
                fake()->longitude,
                10,
            ],
        ];
    }
}
