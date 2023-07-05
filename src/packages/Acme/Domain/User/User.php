<?php

declare(strict_types=1);

namespace Acme\Domain\User;

final readonly class User
{
    public function __construct(
        private UserId $userId,
        private Username $username,
        private ?Email $email,
        private ?Password $password,
    ) {
    }

    public function userId(): string
    {
        return $this->userId->userId;
    }

    public function userIdObject(): UserId
    {
        return $this->userId;
    }

    public function username(): string
    {
        return $this->username->username;
    }

    public function usernameObject(): Username
    {
        return $this->username;
    }

    public function email(): ?string
    {
        return $this->email?->email;
    }

    public function password(): ?string
    {
        return $this->password?->password;
    }
}
