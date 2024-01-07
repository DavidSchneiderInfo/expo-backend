<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class MatchRepository
{
    public function getProfiles(User $user): Collection|array
    {
        return User::query()
            ->whereNot('id', $user->id)
            ->whereNotIn('id', $user->likesToUsers->keyBy('id')->keys())
            ->inRandomOrder()
            ->limit(50)
            ->get();
    }
}
