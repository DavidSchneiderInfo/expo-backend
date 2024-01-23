<?php

namespace App\Http\Resources;

use App\Enums\DistanceUnit;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property Profile $resource
 */
class ProfileResource extends JsonResource
{
    public function __construct(Profile $resource)
    {
        parent::__construct($resource);
        parent::withoutWrapping();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        if($user===null)
        {
            $user = $this->resource->user;
        }
        $profile = $user->profile;

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'bio' => $this->resource->bio,
            'height' => $this->resource->height,
            'age' => $this->resource->age(),
            'sex' => $this->resource->sex,
            'i_f' => $this->resource->i_f,
            'i_m' => $this->resource->i_m,
            'i_x' => $this->resource->i_x,
            'media' => $this->resource->media->toArray(),
            'distance' => $profile->id !== $this->resource->id
                ? $this->distance($profile)
                : 0,
        ];
    }

    private function distance(Profile $profile): int
    {
        // Radius in meter
        $earthRadius = 6371000;

        $latitudeFrom = $profile->latitude;
        $longitudeFrom = $profile->longitude;
        $latitudeTo = $this->resource->latitude;
        $longitudeTo = $this->resource->longitude;

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return (int)(($angle * $earthRadius) / 1000);
    }

}
