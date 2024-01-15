<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\Profile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadMediaControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanUploadMedia(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Sanctum::actingAs($profile->user);

        $this->post($this->getRoute(), [
            'media' => [
                UploadedFile::fake()->image('avatar1.jpg')->size(100),
                UploadedFile::fake()->image('avatar2.jpg')->size(100),
                UploadedFile::fake()->image('avatar3.jpg')->size(100),
            ],
        ])->assertOk()
            ->assertJson([]);

        $this->assertEquals(3, $profile->media()->count());
    }

    public function getRoute(): string
    {
        return 'upload/media';
    }
}
