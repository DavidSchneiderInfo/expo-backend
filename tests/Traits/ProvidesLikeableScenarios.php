<?php

namespace Tests\Traits;

use App\Enums\Sex;
use App\Models\Profile;

trait ProvidesLikeableScenarios
{
    public static function provideLikableScenarios(): array
    {
        return [
            'Female interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                12
            ],
            'Female interested in men and other' => [
                [
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                8
            ],
            'Female interested in women and other' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                8
            ],
            'Female interested in men' => [
                [
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => false,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Female interested in women' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Female interested in other' => [
                [
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::f,
                ],
                4
            ],
            'Male interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::m,
                ],
                12
            ],
            'Male interested in men and other' => [
                [
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::m,
                ],
                8
            ],
            'Male interested in women and other' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::m,
                ],
                8
            ],
            'Male interested in men' => [
                [
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => true,
                    'sex' => Sex::m,
                ],
                4
            ],
            'Male interested in women' => [
                [
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                    'sex' => Sex::m,
                ],
                4
            ],
            'Other interested in all' => [
                [
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                    'sex' => Sex::x,
                ],
                12
            ],
        ];
    }

    private function prepareAllAvailableOptions(int $count = 1): void
    {
        foreach ([
                     Sex::f,
                     Sex::m,
                     Sex::x,
                 ] as $sex)
        {
            // all
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => true,
                ]);
            // no females
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => true,
                ]);
            // no males
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => true,
                ]);
            // no others
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => true,
                    'i_m' => true,
                    'i_x' => false,
                ]);
            // only females
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => true,
                    'i_m' => false,
                    'i_x' => false,
                ]);
            // only males
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => false,
                    'i_m' => true,
                    'i_x' => false,
                ]);
            // only other
            Profile::factory()
                ->count($count)
                ->create([
                    'sex' => $sex,
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => true,
                ]);
        }
    }
}
