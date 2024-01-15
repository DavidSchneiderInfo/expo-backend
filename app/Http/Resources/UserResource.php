<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class UserResource extends JsonResource
{
    public function __construct(private readonly User $user)
    {
        parent::__construct(null);
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
            'id' => $this->user->id,
            'name' => $this->user->profile->name,
            'email' => $this->user->email,
            'birthday' => $this->user->profile->birthday,
            'bio' => $this->user->profile->bio,
        ];
    }
}
