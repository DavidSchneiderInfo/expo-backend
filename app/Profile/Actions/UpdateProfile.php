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
        $this->checkForActivation($user->profile);

        return $user->profile;
    }

    private function checkForActivation(Profile $profile): void
    {
        if(!$profile->active)
        {
            if(!$profile->i_f && !$profile->i_m && !$profile->i_x)
            {
                return;
            }

            $profile->active = true;
            $profile->save();
        }
    }
}
