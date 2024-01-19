<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\RefreshDatabaseFast;

trait TestsAuthentication {
    use RefreshDatabaseFast;

    private function expectedStructure(): array
    {
        return [
            'user' => [
                'name',
                'age',
                'sex',
                'bio',
                'media',
                'height',
            ],
            'token',
            'expires_at'
        ];
    }
}
