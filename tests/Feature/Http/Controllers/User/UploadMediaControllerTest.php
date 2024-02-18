<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\Medium;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Http\Controllers\Auth\TestsResourceFormat;
use Tests\TestCase;

class UploadMediaControllerTest extends TestCase
{
    use TestsResourceFormat;

    public function testUserCanUploadMedia(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Sanctum::actingAs($profile->user);

        $media = $this->post($this->getRoute(), [
            'media' => [
                UploadedFile::fake()->image('avatar1.jpg')->size(100),
                UploadedFile::fake()->image('avatar2.jpg')->size(100),
                UploadedFile::fake()->image('avatar3.jpg')->size(100),
            ],
        ])->assertOk()
            ->assertJsonStructure($this->expectedProfileFormat())
            ->json('media');

        $this->assertCount(3, $media);
        $this->assertEquals(3, $profile->media()->count());

        foreach ($media as $medium)
        {
            $this->assertNotNull(Medium::query()
                ->where('profile_id', $profile->id)
                ->where('path', $medium['path'])
                ->firstOrFail());
        }
    }

    public function getRoute(): string
    {
        return 'upload/media';
    }
}
