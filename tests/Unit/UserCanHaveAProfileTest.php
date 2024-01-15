<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Sex;
use App\Exceptions\ProfileException;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserCanHaveAProfileTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @dataProvider provideUpdateScenarios
     * @throws ProfileException
     */
    public function testNewUserCanCreateProfile(array $attributes): void
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
        $this->assertEquals($profileArray, array_merge($this->defaults(), $attributes));
    }

    public static function provideUpdateScenarios(): array
    {
        return [
            'nothing' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                ],
            ],
            'sex to f' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::f,
                ],
            ],
            'sex to m' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::m,
                ],
            ],
            'sex to x' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'sex' => Sex::x,
                ],
            ],
            'height to 180' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'height' => 180,
                ],
            ],
            'height to 170, sex to m' => [
                [
                    'name' => 'Hans Dampf',
                    'birthday' => '1999-11-21',
                    'height' => 170,
                    'sex' => Sex::m,
                ],
            ],
        ];
    }

    public function createAction(): CreateProfile
    {
        return $this->app->make(CreateProfile::class);
    }

    private function defaults(): array
    {
        return [
            'name' => 'Hans Dampf',
            'birthday' => '1999-11-21',
            'sex' => Sex::x,
            'height' => null,
            'bio' => null,
        ];
    }
}
