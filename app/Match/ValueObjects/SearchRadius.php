<?php

declare(strict_types=1);

namespace App\Match\ValueObjects;

use App\Models\Profile;

class SearchRadius
{
    public readonly float $longitudeMin;
    public readonly float $longitudeMax;
    public readonly float $latitudeMin;
    public readonly float $latitudeMax;

    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
        public readonly int $radius
    )
    {
        $this->longitudeMin = $this->longitude - $radius / abs(cos(deg2rad($this->latitude)) * 69);
        $this->longitudeMax = $this->longitude + $radius / abs(cos(deg2rad($this->latitude)) * 69);
        $this->latitudeMin = $this->latitude - ($radius / 69);
        $this->latitudeMax = $this->latitude + ($radius / 69);
    }

    public static function forProfile(Profile $profile)
    {
        return new SearchRadius(
            $profile->latitude,
            $profile->longitude,
            $profile->maxDistance
        );
    }
}
