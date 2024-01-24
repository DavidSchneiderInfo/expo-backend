<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait TestsAuthentication {
    use RefreshDatabase;

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
                'distance',
            ],
            'token',
            'expires_at'
        ];
    }
}
