<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property User $resource
 */
class ProfileResource extends JsonResource
{
    public function __construct(User $resource)
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

        $media = $this->resource->media()->get();

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'bio' => $this->resource->bio,
            'age' => $age,
            'media' => $media->count()>0
                ? $media->toArray()
                : [],
        ];
    }
}
