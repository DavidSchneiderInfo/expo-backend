<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Match;

use App\Models\Profile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MatchControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testUsersCanMatch(): void
    {
        /** @var Profile $user */
        $user = Profile::factory()->create();
        /** @var Profile $match */
        $match = Profile::factory()->create();

        $match->likesToUsers()->save($user);

        Sanctum::actingAs($user->user);

        $this->post('/match', [
            'user_id' => $match->id,
        ])
            ->assertOk()
            ->assertJson(['match'=>true]);
    }

    public function testUsersOnlySeeUnlikedMatches(): void
    {
        /** @var Profile $user */
        $user = Profile::factory()->create();

        /** @var Profile $liked */
        $liked = Profile::factory()->create();

        $user->likesToUsers()->save($liked);

        Profile::factory()->count(5)->create();

        Sanctum::actingAs($user->user);

        $response = $this->get('/match')
            ->assertOk()
            ->json();

        $this->assertCount(5, $response);
        foreach ($response as $record)
        {
            $this->assertNotNull($user->id);
            $this->assertNotNull($record['id']);
            $this->assertNotEquals($user->id, $record['id']);
        }
    }
}
