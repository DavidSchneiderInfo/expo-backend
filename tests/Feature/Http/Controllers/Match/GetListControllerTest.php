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
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Profile::factory()->count(49)->create([
            'active' => true,
        ]);

        Sanctum::actingAs($profile->user);

        $response = $this->get('/match')
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

        $this->assertCount(49, $response);
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

        $response = $this->get('/match')
            ->assertOk()
            ->json();

        $this->assertCount(25, $response);
    }

    public function testUsersCanLike(): void
    {
        /** @var Profile $user */
        $user = Profile::factory()->create();
        /** @var Profile $match */
        $match = Profile::factory()->create();

        Sanctum::actingAs($user->user);

        $this->post('/match', [
            'user_id' => $match->id,
        ])
            ->assertOk()
            ->assertJson(['match'=>false]);
    }
}
