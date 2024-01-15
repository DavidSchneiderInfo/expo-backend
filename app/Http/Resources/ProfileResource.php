<?php

namespace App\Http\Resources;

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
        $age = $this->resource->birthday !== null
            ? Carbon::createFromFormat("Y-m-d", $this->resource->birthday)->age
            : null;

        $media = $this->resource->media->toArray();

        return [
            'id' => $this->resource->user_id,
            'name' => $this->resource->name,
            'bio' => $this->resource->bio,
            'height' => $this->resource->height,
            'age' => $age,
            'sex' => $this->resource->sex,
            'media' => $media,
        ];
    }
}
