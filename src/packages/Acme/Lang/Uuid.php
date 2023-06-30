<?php

declare(strict_types=1);

namespace Acme\Lang;

use Illuminate\Support\Str;

final class Uuid
{
    public static function generate(): string
    {
        return (string) Str::orderedUuid();
    }
}
