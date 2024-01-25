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
    public function testRepoCanShowResults(array $attributes, int $expectedResults): void
    {
        $profile = Profile::factory()
            ->create($attributes);

        $this->prepareAllAvailableOptions();

        $this->assertEquals($expectedResults, $this->getRepo($profile)->filterGenders()->build()->count());
    }

    public function testRepoShowsResultsWithinRange(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()
            ->withCoordinates(
                fake()->latitude,
                fake()->longitude
            )
            ->create([
                'maxDistance' => 10,
            ]);

        $searchRadius = new SearchRadius($profile->latitude, $profile->longitude, $profile->maxDistance);

        Profile::factory(10)
            ->interestedInProfilesLike($profile)
            ->withinSearchRadius($searchRadius)
            ->create();

        $this->assertMatchRepoCounts(10, $profile);
    }
}
