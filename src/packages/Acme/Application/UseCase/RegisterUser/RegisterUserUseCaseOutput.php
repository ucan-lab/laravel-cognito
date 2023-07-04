<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

final readonly class RegisterUserUseCaseOutput
{
    public function __construct(
        public string $userId,
    ) {
    }
}
