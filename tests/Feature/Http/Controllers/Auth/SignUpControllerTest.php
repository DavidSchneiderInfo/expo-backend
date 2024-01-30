<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    use TestsResourceFormat;

    public function testUserCanSignUp(): void
    {
        $password = Str::password();

        /** @var User $user */
        $user = User::factory()->make([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('auth/sign-up', [
            'email' => $user->email,
            'password' => $password,
            'username' => 'Hans Dampf',
            'birthday' => '1990-06-21',
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure($this->expectedAuthFormat());
    }

    public function testUserCantSignUpWithExistingEmail(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $password = Str::password();

        $this->post('auth/sign-up', [
            'email' => $user->email,
            'password' => $password,
            'passwordRepeat' => $password,
            'username' => 'Example Name',
            'birthday' => '1990-09-21',
        ])
            ->assertStatus(409);
    }
}
