<?php

namespace Database\Factories;

use App\Enums\Sex;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'birthday' => fake()->dateTimeBetween('-50 years', '-18 years'),
            'bio' => fake()->words(rand(10,20), true),
            'sex' => collect([
                Sex::f,
                Sex::m,
                Sex::x,
            ])->random(),
            'height' => rand(150,210),
            'active' => true,
        ];
    }
}
