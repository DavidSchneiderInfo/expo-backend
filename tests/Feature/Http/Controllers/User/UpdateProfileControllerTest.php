<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Exceptions\ProfileException;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\RefreshDatabaseFast;

class UpdateProfileControllerTest extends TestCase
{
    use RefreshDatabaseFast;

    /**
     * @dataProvider providePersonalDetailsPatches
     * @throws ProfileException
     */
    public function testUserCanUpdatePersonalDetails(array $requestData): void
    {
        $user = User::factory()->create();
        $this->createAction()->create($user, [
            'name' => 'Hans Peter',
            'birthday' => '1981-10-20',
        ]);
        $user->refresh();

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
                ],
            ],
            'just the bio' => [
                [
                    'bio' => 'ipsum lorem',
                ],
            ],
            'just the sex' => [
                [
                    'sex' => 'f',
                ],
            ],
            'just the height' => [
                [
                    'height' => 199,
                ],
            ],
            'same name, different bio' => [
                [
                    'name' => 'Hans Peter',
                    'bio' => 'ipsum lorem',
                ],
            ],
            'interested in female' => [
                [
                    'i_f' => true,
                ]
            ],
            'interested in male' => [
                [
                    'i_m' => true,
                ]
            ],
            'interested in other' => [
                [
                    'i_x' => true,
                ]
            ],
            'interested in female + other' => [
                [
                    'i_f' => true,
                    'i_x' => true,
                ]
            ],
            'interested in other + male' => [
                [
                    'i_x' => true,
                    'i_m' => true,
                ]
            ],
            'interested in all' => [
                [
                    'i_f' => true,
                    'i_x' => true,
                    'i_m' => true,
                ]
            ],
        ];
    }

    private function createAction(): CreateProfile
    {
        return $this->app->make(CreateProfile::class);
    }
}
