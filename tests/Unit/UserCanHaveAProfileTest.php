<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Sex;
use App\Exceptions\ProfileException;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use Tests\TestCase;
use Tests\Traits\RefreshDatabaseFast;

class UserCanHaveAProfileTest extends TestCase
{
    use RefreshDatabaseFast;

    /**
     * @dataProvider provideUpdateScenarios
     * @throws ProfileException
     */
    public function testNewUserCanCreateProfile(array $attributes, array $expected): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $profile = $this->createAction()->create($user, $attributes);
        $user->refresh();

        // Check relationship basics
        $this->assertNotNull($user->profile);
        $this->assertEquals($user->profile->user_id, $user->id);

        // check public data
        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('updated_at', $profileArray);
        unset($profileArray['updated_at']);
        $this->assertEquals($profileArray, $expected);
    }

    public static function provideUpdateScenarios(): array
    {
        return [
            'nothing' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::x,
                    'height' => null,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
            'sex to f' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::f,
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::f,
                    'height' => null,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
            'sex to m' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::m,
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::m,
                    'height' => null,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
            'sex to x' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::x,
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::x,
                    'height' => null,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
            'height to 180' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'height' => 180,
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::x,
                    'height' => 180,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
            'height to 170, sex to m' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'height' => 170,
                    'sex' => Sex::m,
                ],
                [
                    'name' => 'Hans Dampf',
                    'age' => 24,
                    'media' => [],
                    'sex' => Sex::m,
                    'height' => 170,
                    'bio' => '',
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                    'maxDistance' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'avatar' => null,
                    'cover' => null,
                ],
            ],
        ];
    }

    public function createAction(): CreateProfile
    {
        return $this->app->make(CreateProfile::class);
    }
}
