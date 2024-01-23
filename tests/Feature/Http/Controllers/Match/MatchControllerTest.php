<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Match;

use App\Models\Profile;
use Tests\RefreshDatabaseFast;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MatchControllerTest extends TestCase
{
    use RefreshDatabaseFast;

    public function testUsersCanMatch(): void
    {
        $profile = Profile::factory()->create();
        $match = Profile::factory()->create();

        $match->likesToUsers()->save($profile);

        $this->assertNotNull($profile->likesFromUsers->find($match->id));

        Sanctum::actingAs($profile->user);

        $this->post('/match', [
            'user_id' => $match->id,
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
        ])
            ->assertOk()
            ->assertJson(['match'=>true]);
    }

    public function testUsersCanLike(): void
    {
        $user = Profile::factory()->create();
        $match = Profile::factory()->create();

        Sanctum::actingAs($user->user);

        $this->post('/match', [
            'user_id' => $match->id,
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
        ])
            ->assertOk()
            ->assertJson(['match'=>false]);
    }
}
