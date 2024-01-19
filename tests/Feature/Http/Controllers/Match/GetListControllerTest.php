<?php

namespace Tests\Feature\Http\Controllers\Match;

use App\Models\Profile;
use App\Profile\Actions\CreateProfile;
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
            ->json();

        $this->assertCount(49, $response);
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
