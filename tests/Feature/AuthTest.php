<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function testDuplicateEmailThrowsError(): void
    {
        $user = User::factory()->create();

        $this->post('auth/sign-up', [
            'email' => $user->email,
            'password' => Str::random(),
            'username' => Str::random(),
            'birthday' => $user->birthday->format('Y-m-d'),
        ])->assertInvalid([
            'email' => [
                'The email has already been taken.'
            ]
        ]);
    }

    public function testUserCanSignIn(): void
    {
        $password = Str::password();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('auth/sign-in', [
                'email' => $user->email,
                'password' => $password,
            ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'user' => [
                    'name',
                    'birthday',
                ],
                'token',
            ]);

        $this->withHeader('Authorization', 'Bearer '.$response->json('token'))
            ->get('user')
            ->assertSuccessful();
    }

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
                'passwordRepeat' => $password,
                'username' => $user->name,
                'birthday' => $user->birthday->format('Y-m-d'),
            ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'user' => [
                    'name',
                    'birthday',
                ],
                'token',
            ]);

        $this->withHeader('Authorization', 'Bearer '.$response->json('token'))
            ->get('user')
            ->assertSuccessful();
    }
}
