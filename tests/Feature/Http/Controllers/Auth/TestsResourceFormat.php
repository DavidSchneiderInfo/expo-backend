<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\Traits\RefreshDatabaseFast;

trait TestsResourceFormat {
    use RefreshDatabaseFast;

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
