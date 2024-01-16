<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property User $user
 */
class SuccessfulAuthenticationResource extends JsonResource
{
    public function __construct(
        private readonly User $user,
        private readonly NewAccessToken $token
    )
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
        $profile = new ProfileResource($this->user->profile);
        return [
            'user' => $profile->toArray($request),
            'token' => $this->token->plainTextToken,
            'expires_at' => $this->token->accessToken->expires_at,
        ];
    }

}
