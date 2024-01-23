<?php

namespace Database\Factories;

use App\Enums\Sex;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @extends Factory<Profile>
 * @method Profile|Collection create($attributes = [], ?Model $parent = null)
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
        $gender = collect([
            Sex::f,
            Sex::m,
            Sex::x,
        ])->random();

        $name = match ($gender) {
            'f' => fake()->firstNameFemale,
            'm' => fake()->firstNameMale,
            'x' => fake()->firstName,
        };

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'birthday' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'bio' => fake()->words(rand(10,20), true),
            'sex' => $gender,
            'height' => rand(150,210),
            'active' => true,
            'i_f' => true,
            'i_m' => true,
            'i_x' => true,
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
            'maxDistance' => null,
        ];
    }

    public function withinCoordinates(float $lat_min, float $lat_max, float $lng_min, float $lng_max): self
    {
        return $this->state(function (array $attributes) use ($lat_min, $lat_max, $lng_min, $lng_max) {
            return array_merge($attributes, [
                'latitude' => fake()->latitude($lat_min, $lat_max),
                'longitude' =>  fake()->latitude($lng_min, $lng_max),
            ]);
        });
    }

    public function withCoordinates(float $latitude, float $longitude): self
    {
        return $this->state(function (array $attributes) use ($latitude, $longitude) {
            return array_merge($attributes, [
                'latitude' => $latitude,
                'longitude' =>  $longitude,
            ]);
        });
    }

    public function interestedInProfilesLike(Profile $profile): self
    {
        return $this->state(function (array $attributes) use ($profile) {
            $attributes['i_f'] = false;
            $attributes['i_m'] = false;
            $attributes['i_x'] = false;
            switch($profile->sex) {
                case Sex::f:
                    $attributes['i_f'] = true;
                    $attributes['sex'] = ($profile->i_f)
                        ? Sex::f
                        : Sex::m;
                    break;
                case Sex::m:
                    $attributes['i_m'] = true;
                    $attributes['sex'] = ($profile->i_m)
                        ? Sex::m
                        : Sex::f;
                    break;
                case Sex::x:
                    $attributes['i_x'] = true;
                    $attributes['sex'] = Sex::x;
                    break;
            }

            return $attributes;
        });
    }

    public function notInterestedInProfilesLike(Profile $profile): self
    {
        return $this->state(function (array $attributes) use ($profile) {
            $attributes['i_f'] = true;
            $attributes['i_m'] = true;
            $attributes['i_x'] = true;
            $key = 'i_'.$profile->sex;
            $attributes[$key] = false;
            return $attributes;
        });
    }
}
