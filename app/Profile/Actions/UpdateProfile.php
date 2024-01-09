<?php

declare(strict_types=1);

namespace App\Profile\Actions;

use App\Exceptions\ProfileException;
use App\Models\Profile;
use App\Models\User;

class UpdateProfile
{
    /**
     * @throws ProfileException
     */
    public function update(User $user, array $attributes): Profile
    {
        if($user->profile===null)
        {
            throw ProfileException::missing();
        }

        $user->profile->update($attributes);

        return $user->profile;
    }
}
