<?php

namespace Tests\Traits;

use App\Enums\Sex;

trait ProvidesLikeableScenarios
{
    public static function provideLikableScenarios(): array
    {
        $cases = [];

        foreach ([
                     Sex::f,
                     Sex::m,
                     Sex::x
                 ] as $gender)
        {
            foreach ([
                         Sex::f,
                         Sex::m,
                         Sex::x
                     ] as $likes)
            {
                $attributes = [
                    'sex' => $gender,
                    'i_f' => false,
                    'i_m' => false,
                    'i_x' => false,
                ];
                $attributes['i_'.$likes] = true;

                $cases[$gender.' liking '.$likes] = [
                    $attributes,
                ];
            }
        }

        return $cases;
    }
}
