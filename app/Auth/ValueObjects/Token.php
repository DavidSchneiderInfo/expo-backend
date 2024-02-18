<?php

namespace App\Auth\ValueObjects;

use Illuminate\Support\Carbon;

class Token
{
    public function __construct(
        public readonly string $plainTextToken,
        public readonly Carbon $expiresAt
    ) {}
}
