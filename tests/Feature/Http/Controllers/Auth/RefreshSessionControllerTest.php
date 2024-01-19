<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Profile;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RefreshSessionControllerTest extends TestCase
{
    use TestsAuthentication;

    public function testRefreshingASession(): void
    {
        $user = User::factory()
            ->has(
                Profile::factory()
            )->create();

        Sanctum::actingAs($user);

        $response = $this->post('auth/refresh');

        $response->assertSuccessful()
            ->assertJsonStructure($this->expectedStructure());

        $this->assertIsBool($response->json('active'));
    }
}
