<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait TestsResourceFormat {
    use RefreshDatabase;

    private function expectedAuthFormat(): array
    {
        return [
            'user' => [
                'name',
                'age',
                'sex',
                'bio',
                'media' => [],
                'height',
                'distance',
            ],
            'token',
            'expires_at'
        ];
    }

    private function expectedProfileFormat(): array
    {
        return [
            'name',
            'age',
            'sex',
            'bio',
            'media' => [],
            'height',
            'distance',
        ];
    }
}
