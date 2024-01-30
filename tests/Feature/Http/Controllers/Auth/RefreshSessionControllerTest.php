<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Profile;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RefreshSessionControllerTest extends TestCase
{
    use TestsResourceFormat;

    public function testRefreshingASession(): void
    {
        /** @var Profile $profile */
        $profile = Profile::factory()->create();

        Sanctum::actingAs($profile->user);

        $response = $this->post('auth/refresh');

        $response->assertSuccessful()
            ->assertJsonStructure($this->expectedAuthFormat());

        $this->assertIsBool($response->json('active'));
        $this->assertIsBool($response->json('user.i_f'));
        $this->assertIsBool($response->json('user.i_m'));
        $this->assertIsBool($response->json('user.i_x'));
    }
}
