<?php

declare(strict_types=1);

namespace Acme\Domain\User;

final readonly class Password
{
    public function __construct(
        public string $password,
    ) {
    }
}
