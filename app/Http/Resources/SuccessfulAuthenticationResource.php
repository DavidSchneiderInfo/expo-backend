<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Auth\ValueObjects\Token;
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
        private readonly Token $token
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
        return [
            'user' => new ProfileResource($this->user->profile),
            'active' => $this->user->profile->active,
            'token' => $this->token->plainTextToken,
            'expires_at' => $this->token->expiresAt,
        ];
    }

}
