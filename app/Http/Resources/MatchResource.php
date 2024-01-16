<?php

namespace App\Http\Resources;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property Profile $resource
 */
class MatchResource extends JsonResource
{
    public function __construct(private readonly bool $match)
    {
        parent::__construct($match);
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
            'match' => $this->match,
        ];
    }
}
