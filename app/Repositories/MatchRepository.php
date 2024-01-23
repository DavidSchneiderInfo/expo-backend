<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Sex;
use App\Match\ValueObjects\SearchRadius;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;

final class MatchRepository
{
    private Builder $query;

    private function __construct(private readonly Profile $profile) {
        $this->query = Profile::query()
            ->inRandomOrder()
            ->whereNot('id', $this->profile->id)
            ->whereNotIn('id', $this->profile->likesToUsers->keyBy('id')->keys())
            ->where('active', true)
            ->clone();
    }

    public static function forProfile(Profile $profile): self
    {
        return new self($profile);
    }

    public function getProfiles(): Builder
    {
        return $this
            ->filterGenders()
            ->filterDistance()
            ->build();
    }

    public function build(): Builder
    {
        return $this->query;
    }

    public function filterGenders(): self
    {
        $sexes = [];
        foreach ([
                     $this->profile->i_f => Sex::f,
                     $this->profile->i_m => Sex::m,
                     $this->profile->i_x => Sex::x,
                 ] as $interested => $sex)
        {
            if($interested)
                $sexes[] = $sex;
        }
        $this->query->whereIn('sex', $sexes)
            ->where('i_'.$this->profile->sex, true);

        return $this;
    }

    public function filterDistance(): self
    {
        if($this->profile->maxDistance !== null)
        {
            $searchRadius = new SearchRadius(
                $this->profile->latitude,
                $this->profile->longitude,
                $this->profile->maxDistance
            );

            $this->query->whereBetween('latitude', [
                $searchRadius->latitudeMin,
                $searchRadius->latitudeMax
            ])
                ->whereBetween('longitude', [
                    $searchRadius->longitudeMin,
                    $searchRadius->longitudeMax
                ]);
        }

        return $this;
    }
}
