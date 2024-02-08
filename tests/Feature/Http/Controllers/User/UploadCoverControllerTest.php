<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\Medium;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Http\Controllers\Auth\TestsResourceFormat;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadCoverControllerTest extends TestCase
{
    use RefreshDatabase;
    use TestsResourceFormat;

    /**
     * @dataProvider provideFilenameScenarios
     */
    public function testUserCanUploadAvatar($filename): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Sanctum::actingAs($profile->user);

        $avatar = $this->post($this->getRoute(), [
            'cover' => UploadedFile::fake()->image($filename)->size(100),
        ])->assertOk()
            ->assertJsonStructure($this->expectedProfileFormat())
            ->json('cover');

        $this->assertEquals($profile->id . '/cover.jpg', $avatar);

        $this->get('images/'.$avatar)
            ->assertOk();
    }

    public static function provideFilenameScenarios(): array
    {
        return [
            'JPEG' => [
                'cover1.jpeg'
            ],
            'JPG' => [
                'bla.jpg'
            ],
        ];
    }

    public function getRoute(): string
    {
        return 'user/cover';
    }
}
