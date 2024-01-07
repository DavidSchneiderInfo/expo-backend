<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MatchTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanGetAList(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Hans Peter',
            'email' => 'hans@peter.com',
            'bio' => 'lorem ipsum',
            'birthday' => '1981-10-20',
        ]);

        User::factory()->count(50)->create();

        Sanctum::actingAs($user);

        $response = $this->get('/match')
            ->assertOk()
            ->json();

        $this->assertCount(50, $response);
        foreach ($response as $record)
        {
            $this->assertEquals(
                [
                    'id',
                    'name',
                    'bio',
                    'age',
                    'media' => [],
                ],
                array_keys($record)
            );
            $this->assertNotEquals($user->id, $record['id']);
        }
    }

    public function testUsersCanLike(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $match */
        $match = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post('/match', [
            'user_id' => $match->id,
        ])
            ->assertOk()
            ->assertJson(['match'=>false]);
    }

    public function testUsersCanMatch(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $match */
        $match = User::factory()->create();

        $match->likesToUsers()->save($user);

        Sanctum::actingAs($user);

        $this->post('/match', [
            'user_id' => $match->id,
        ])
            ->assertOk()
            ->assertJson(['match'=>true]);
    }

    public function testUsersOnlySeeNewMatches(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var User $liked */
        $liked = User::factory()->create();

        $user->likesToUsers()->save($liked);

        User::factory()->count(5)->create();

        Sanctum::actingAs($user);

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
