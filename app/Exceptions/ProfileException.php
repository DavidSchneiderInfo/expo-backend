<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ProfileException extends Exception
{
    public static function existing(): self
    {
        return new self('This user has a profile already.');
    }

    public static function missing(): self
    {
        return new self('This user has no profile.');
    }
}
