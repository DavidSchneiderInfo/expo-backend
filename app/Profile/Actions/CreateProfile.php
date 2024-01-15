<?php

declare(strict_types=1);

namespace App\Profile\Actions;

use App\Exceptions\ProfileException;
use App\Models\Profile;
use App\Models\User;

class CreateProfile
{
    /**
     * @throws ProfileException
     */
    public function create(User $user, array $attributes): Profile
    {
        if($user->profile!==null)
        {
            throw ProfileException::existing();
        }

        $profile = new Profile($attributes);

        $user->profile()->save($profile);
        $profile->refresh();
        $user->refresh();

        return $profile;
    }
}
