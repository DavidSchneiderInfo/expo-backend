<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use TestsAuthentication;

    public function testUserCanSignIn(): void
    {
        $profile = Profile::factory()->create([]);

        $password = Str::password();
        /** @var User $user */
        $user = User::query()->findOrFail($profile->user_id);
        $user->update([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('auth/sign-in', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure($this->expectedStructure());
    }

    public function testUsersCanNotLoginWithWrongPassword(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->post('auth/sign-in', [
            'email' => $user->email,
            'password' => Str::password(),
        ])
            ->assertStatus(401);
    }
}
