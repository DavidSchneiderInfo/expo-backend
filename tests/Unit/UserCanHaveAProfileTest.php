<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\ProfileException;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use App\Profile\Actions\UpdateProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserCanHaveAProfileTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @throws ProfileException
     * @dataProvider provideCreateScenarios
     */
    public function testNewUserCanCreateProfile(array $attributes): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertNull($user->profile);

        $profile = $this->getCreateAction()->create($user, $attributes);

        $user->refresh();
        $this->assertNotNull($user->profile);
        $this->assertNotNull($profile);
        $this->assertEquals($user->id, $profile->user_id);

        // check public data
        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('updated_at', $profileArray);
        unset($profileArray['updated_at']);
        $this->assertEquals($profileArray, $attributes);
    }

    public function provideCreateScenarios(): array
    {
        return [
            'nothing' => [
                [],
            ],
        ];
    }

    /**
     * @throws ProfileException
     * @dataProvider provideUpdateScenarios
     */
    public function testUserWithProfileCanUpdateProfile($attributes, $expected): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $profile = $this->getCreateAction()->create($user, []);

        $user->refresh();
        $this->assertNotNull($user->profile);
        $this->assertNotNull($profile);

        $profile = $this->getUpdateAction()->update($user, $attributes);

        $user->refresh();
        $profile->refresh();

        // check public data
        $profileArray = $profile->toArray();
        $this->assertArrayHasKey('updated_at', $profileArray);
        unset($profileArray['updated_at']);
        $this->assertEquals($profileArray, $expected);
    }

    public function provideUpdateScenarios(): array
    {
        return [
            'nothing' => [
                [],
                [],
            ],
        ];
    }

    private function getCreateAction(): CreateProfile {
        return $this->app->make(CreateProfile::class);
    }

    private function getUpdateAction(): UpdateProfile {
        return $this->app->make(UpdateProfile::class);
    }
}
