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
        return [
            'id' => $this->resource->user_id,
            'name' => $this->resource->name,
            'bio' => $this->resource->bio,
            'height' => $this->resource->height,
            'age' => $this->resource->age(),
            'sex' => $this->resource->sex,
            'i_f' => $this->resource->i_f,
            'i_m' => $this->resource->i_m,
            'i_x' => $this->resource->i_x,
            'media' => $this->resource->media->toArray(),
        ];
    }
}
