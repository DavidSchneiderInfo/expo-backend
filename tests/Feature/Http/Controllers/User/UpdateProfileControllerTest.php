<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Exceptions\ProfileException;
use App\Models\User;
use App\Profile\Actions\CreateProfile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProfileControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @dataProvider providePersonalDetailsPatches
     * @throws ProfileException
     */
    public function testUserCanUpdatePersonalDetails(array $requestData): void
    {
        $user = User::factory()->create();
        $this->updateProfile()->create($user, [
            'name' => 'Hans Peter',
            'email' => 'hans@peter.com',
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
                    'bio' => 'lorem ipsum',
                    'birthday' => '1981-10-20',
                ],
            ],
            'all of it' => [
                [
                    'name' => 'Peter Hans',
                    'bio' => 'ipsum lorem',
                    'birthday' => '1911-01-02',
                ],
            ],
            'same name' => [
                [
                    'name' => 'Hans Peter',
                    'bio' => 'ipsum lorem',
                    'birthday' => '1981-10-20',
                ],
            ],
        ];
    }

    private function updateProfile(): CreateProfile
    {
        return $this->app->make(CreateProfile::class);
    }
}
