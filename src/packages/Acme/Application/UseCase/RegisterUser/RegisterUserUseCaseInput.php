<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

final readonly class RegisterUserUseCaseInput
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
    ) {
    }
}
