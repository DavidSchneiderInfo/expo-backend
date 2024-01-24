<?php

declare(strict_types=1);

namespace App\Match\ValueObjects;

class SearchRadius
{
    public readonly float $longitudeMin;
    public readonly float $longitudeMax;
    public readonly float $latitudeMin;
    public readonly float $latitudeMax;

    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
        int $radius
    )
    {
        $this->longitudeMin = $this->longitude - $radius / abs(cos(deg2rad($this->latitude)) * 69);
        $this->longitudeMax = $this->longitude + $radius / abs(cos(deg2rad($this->latitude)) * 69);
        $this->latitudeMin = $this->latitude - ($radius / 69);
        $this->latitudeMax = $this->latitude + ($radius / 69);
    }
}
