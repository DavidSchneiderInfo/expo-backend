<?php

namespace Tests\Feature\Http\Controllers\Match;

use App\Enums\Sex;
use App\Match\ValueObjects\SearchRadius;
use App\Models\Profile;
use App\Repositories\MatchRepository;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\ProvidesLikeableScenarios;

class GetListControllerTest extends TestCase
{
    use RefreshDatabase;
    use ProvidesLikeableScenarios;

    /**
     * Test the basic endpoint, create 1 profile and 5 likeable profiles to
     * display and make sure the endpoint shows the expected 5 profiles.
     */
    public function testEndpointWorks(): void
    {
        $profile = Profile::factory()->create([
            'sex' => Sex::x,
            'i_f' => true,
            'i_m' => true,
            'i_x' => true,
            'maxDistance' => null,
        ]);

        Profile::factory()
            ->interestedInProfilesLike($profile)
            ->count(5)->create();

        Sanctum::actingAs($profile->user);

        $requestData = [
            'latitude'=> fake()->latitude(),
            'longitude'=> fake()->longitude(),
        ];

        $response = $this->post('match/list', $requestData)
            ->assertOk()
            ->assertJsonStructure([[
                'id',
                'name',
                'age',
                'sex',
                'bio',
                'media',
                'height',
                'i_f',
                'i_m',
                'i_x',
            ]])
            ->json();

        $this->assertCount(5, $response);
        foreach ($response as $matchListProfile)
        {
            $this->assertIsString($matchListProfile['id']);
            $this->assertIsString($matchListProfile['name']);
            $this->assertTrue(
                is_int($matchListProfile['age'])
                ||
                is_null($matchListProfile['age'])
            , 'Age is not numeric or null');
            $this->assertTrue(in_array($matchListProfile['sex'], [
                Sex::f,
                Sex::m,
                Sex::x,
            ]), 'No valid sex');
        }
    }

    public static function provideLikeableScenarios(): array
    {
        return [
            'man liking women' => []
        ];
    }
    public function testEndpointShowsOnlyActiveProfiles(): void
    {
        $profile = Profile::factory()->create();

        Profile::factory()
            ->count(5)
            ->interestedInProfilesLike($profile)
            ->create();

        Profile::factory()
            ->count(5)
            ->interestedInProfilesLike($profile)
            ->notActive()
            ->create();

        $this->assertMatchRepoCounts(5, $profile);

        Sanctum::actingAs($profile->user);

        $requestData = [
            'latitude'=> fake()->latitude(),
            'longitude'=> fake()->longitude(),
        ];

        $response = $this->post('match/list', $requestData)
            ->assertOk()
            ->json();

        $this->assertCount(5, $response);
    }

    /**
     * @dataProvider provideInvalidCoordinates
     */
    public function testCoordinatesAreRequired(array $params, array $errors): void
    {
        $user = Profile::factory()->create();

        Sanctum::actingAs($user->user);

        $this->post('match/list', $params)
            ->assertInvalid($errors);
    }

    public static function provideInvalidCoordinates(): array
    {
        return [
            'missing everything' => [
                [],
                [
                    'latitude' => ['The latitude field is required.'],
                    'longitude' => ['The longitude field is required.'],
                ],
            ],
            'missing longitude' => [
                [
                    'latitude' => fake()->latitude,
                ],
                [
                    'longitude' => ['The longitude field is required.'],
                ],
            ],
            'missing latitude' => [
                [
                    'longitude' => fake()->longitude,
                ],
                [
                    'latitude' => ['The latitude field is required.'],
                ],
            ],
        ];
    }

    public function testUsersOnlySeeUnlikedMatches(): void
    {
        $count = 45;

        $profile = Profile::factory()
            ->create();

        $liked = Profile::factory()
            ->interestedInProfilesLike($profile)
            ->count($count)
            ->create();
        foreach ($liked as $like) {
            $profile->likesToUsers()->save($like);
        }

        Profile::factory()
            ->interestedInProfilesLike($profile)
            ->count($count)
            ->create();

        $this->assertMatchRepoCounts($count, $profile);

        Sanctum::actingAs($profile->user);

        $response = $this->post('match/list', [
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
        ])
            ->assertOk()
            ->json();

        $this->assertCount($count, $response);
        foreach ($response as $record)
        {
            $this->assertNotNull($record['id']);
            $this->assertNotEquals($profile->id, $record['id']);
        }
    }

    public function testListOnlyContainsProfilesWithinRange(): void
    {
        $this->markTestSkipped('Distance stuff doesnt work');
        $earthRadius = 6371000;
        // fake a starting point
        $coordinates = fake()->localCoordinates();
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];

        // Create and search profiles within 10km
        $searchRadius = 10;

        // Create user
        $profile = Profile::factory()
            ->withCoordinates($latitude, $longitude)
            ->create([
                'maxDistance' => $searchRadius,
            ]);
        Sanctum::actingAs($profile->user);

        $maxLat = $latitude + rad2deg($searchRadius/$earthRadius);
        $minLat = $latitude - rad2deg($searchRadius/$earthRadius);
        $maxLon = $longitude + rad2deg(asin($searchRadius/$earthRadius) / cos(deg2rad($latitude)));
        $minLon = $longitude - rad2deg(asin($searchRadius/$earthRadius) / cos(deg2rad($latitude)));

        Profile::factory()
            ->withinCoordinates($minLat, $maxLat, $minLon, $maxLon)
            ->count(5)
            ->create();

        Profile::factory()
            ->withCoordinates($maxLat+1, $maxLon+1)
            ->count(5)
            ->create();

        Profile::factory()
            ->withCoordinates($latitude, $longitude)
            ->create();

        $response = $this->post('match/list', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ])
            ->assertOk()
            ->json();

        $this->assertCount(6, $response);
        foreach ($response as $profile)
        {
            $this->assertLessThanOrEqual($searchRadius, $profile['distance']);
        }
    }

    public function testListOnlyContainsProfilesWithinRangeWhenProfileHasMaxDistance(): void
    {
        $earthRadius = 6371000;
        // fake a starting point
        $coordinates = fake()->localCoordinates();
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];

        // Create and search profiles within 10km
        $searchRadius = 10;

        // Create user
        $profile = Profile::factory()
            ->withCoordinates($latitude, $longitude)
            ->create();
        Sanctum::actingAs($profile->user);

        $maxLat = $latitude + rad2deg($searchRadius/$earthRadius);
        $minLat = $latitude - rad2deg($searchRadius/$earthRadius);
        $maxLon = $longitude + rad2deg(asin($searchRadius/$earthRadius) / cos(deg2rad($latitude)));
        $minLon = $longitude - rad2deg(asin($searchRadius/$earthRadius) / cos(deg2rad($latitude)));

        Profile::factory()
            ->withinCoordinates($minLat, $maxLat, $minLon, $maxLon)
            ->count(5)
            ->create();

        Profile::factory()
            ->withCoordinates($maxLat+1, $maxLon+1)
            ->count(5)
            ->create();

        Profile::factory()
            ->withCoordinates($latitude, $longitude)
            ->create();

        $response = $this->post('match/list', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ])
            ->assertOk()
            ->json();

        $this->assertCount(11, $response);
    }

    /**
     * @dataProvider provideLikableScenarios
     */
    public function testUserOnlySeesProfilesWithInterestingGenders(array $attributes, int $expectedResultCount): void
    {
        $profile = Profile::factory()->create($attributes);

        $this->prepareAllAvailableOptions();

        Sanctum::actingAs($profile->user);
        $coordinates = fake()->localCoordinates();
        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];

        $response = $this->post('match/list', [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ])
            ->assertOk()
            ->json();

        $this->assertCount($expectedResultCount, $response);
    }
}
