<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadMediaTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserCanUploadMedia(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post($this->getRoute(), [
                'media' => [
                    UploadedFile::fake()->image('avatar1.jpg')->size(100),
                    UploadedFile::fake()->image('avatar2.jpg')->size(100),
                    UploadedFile::fake()->image('avatar3.jpg')->size(100),
                ],
            ])->assertOk()
            ->assertJson([]);

        $this->assertEquals(3, $user->media()->count());
    }

    public function getRoute(): string
    {
        return 'upload/media';
    }
}
