<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;

trait TestsAuthentication {
    use DatabaseMigrations;

    private function expectedStructure(): array
    {
        return [
            'user' => [
                'name',
                'birthday',
            ],
            'token',
            'expires_at'
        ];
    }
}
