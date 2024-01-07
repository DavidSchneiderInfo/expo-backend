<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserDetailsAreCorrect(): void
    {
        $user = User::factory()->create([
            'name' => 'Hans Peter',
            'email' => 'hans@peter.com',
            'bio' => 'lorem ipsum',
            'birthday' => '1981-10-20',
        ]);
        Sanctum::actingAs($user);

        $response = $this->get('/user');

        $response->assertOk()
            ->assertJson([
                'id' => $user->id,
                'name' => 'Hans Peter',
                'email' => 'hans@peter.com',
                'bio' => 'lorem ipsum',
                'birthday' => '1981-10-20',
            ]);
    }

    /**
     * @dataProvider providePersonalDetailsPatches
     */
    public function testUserCanUpdatePersonalDetails(array $requestData): void
    {
        $user = User::factory()->create([
            'name' => 'Hans Peter',
            'email' => 'hans@peter.com',
            'bio' => 'lorem ipsum',
            'birthday' => '1981-10-20',
        ]);

        Sanctum::actingAs($user);

        $this->patch('/user', $requestData)
            ->assertOk()
            ->assertJson($requestData);
    }

    public static function providePersonalDetailsPatches(): array
    {
        return [
            'just the name' => [
                [
                    'name' => 'Peter Hans',
                    'email' => 'hans@peter.com',
                    'bio' => 'lorem ipsum',
                    'birthday' => '1981-10-20',
                ],
            ],
            'all of it' => [
                [
                    'name' => 'Peter Hans',
                    'email' => 'peter@hans.com',
                    'bio' => 'ipsum lorem',
                    'birthday' => '1911-01-02',
                ],
            ],
            'same name and email' => [
                [
                    'name' => 'Hans Peter',
                    'email' => 'hans@peter.com',
                    'bio' => 'ipsum lorem',
                    'birthday' => '1981-10-20',
                ],
            ],
        ];
    }
}
