<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;

final class MatchRepository
{
    private function __construct(private readonly Profile $profile) {}
    public static function forProfile(Profile $profile): self
    {
        return new self($profile);
    }
    public function getProfiles(): Builder
    {
        return Profile::query()
            ->inRandomOrder()
            ->whereNot('id', $this->profile->id)
            ->whereNotIn('id', $this->profile->likesToUsers->keyBy('id')->keys())
            ->where('active', true);
    }
}
