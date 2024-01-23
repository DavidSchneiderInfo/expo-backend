<?php

namespace Tests\Feature\Http\Controllers\Match;

use App\Enums\Sex;
use App\Models\Profile;
use Tests\RefreshDatabaseFast;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetListControllerTest extends TestCase
{
    use RefreshDatabaseFast;

    /**
     * A basic feature test example.
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

    public function testEndpointShowsOnlyActiveProfiles(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Profile::factory()->count(25)->create([
            'active' => false,
        ]);
        Profile::factory()->count(25)->create([
            'active' => true,
        ]);

        Sanctum::actingAs($profile->user);

        $requestData = [
            'latitude'=> fake()->latitude(),
            'longitude'=> fake()->longitude(),
        ];

        $response = $this->post('match/list', $requestData)
            ->assertOk()
            ->json();

        $this->assertCount(25, $response);
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
        $profile = Profile::factory()->create();
        $liked = Profile::factory()->create();
        Profile::factory()->count(5)->create();

        $profile->likesToUsers()->save($liked);

        $this->assertNotNull($liked->id);

        Sanctum::actingAs($profile->user);

        $response = $this->post('match/list', [
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
        ])
            ->assertOk()
            ->json();

        $this->assertCount(5, $response);
        foreach ($response as $record)
        {
            $this->assertNotNull($record['id']);
            $this->assertNotEquals($profile->id, $record['id']);
        }
    }

    public function testListOnlyContainsProfilesWithinRange(): void
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
     * @dataProvider provideSexInterestScenarios
     */
    public function testUserOnlySeesProfilesWithInterestingGenders(array $attributes, int $expectedResultCount): void
    {
        $profile = Profile::factory()->create($attributes);

        foreach ([
            Sex::f,
            Sex::m,
            Sex::x,
         ] as $sex)
        {
            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => true,
                'i_m' => true,
                'i_x' => true,
            ]);
            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => false,
                'i_m' => true,
                'i_x' => true,
            ]);
            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => true,
                'i_m' => false,
                'i_x' => true,
            ]);
            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => false,
                'i_m' => false,
                'i_x' => true,
            ]);

            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => true,
                'i_m' => true,
                'i_x' => false,
            ]);

            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => false,
                'i_m' => true,
                'i_x' => false,
            ]);

            Profile::factory()->create([
                'sex' => $sex,
                'i_f' => true,
                'i_m' => false,
                'i_x' => false,
            ]);
        }

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

    public static function provideSexInterestScenarios(): array
    {
        return [
            'Female interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Female interested in men and other' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Female interested in women and other' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Female interested in men' => [
                [
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => false,
                    'sex' => Sex::f,
                ],
                11
            ],
            'Female interested in women' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                    'sex' => Sex::f,
                ],
                11
            ],
            'Female interested in other' => [
                [
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                11
            ],
            'Male interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::m,
                ],
                11
            ],
            'Other interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::x,
                ],
                11
            ],
        ];
    }
}
