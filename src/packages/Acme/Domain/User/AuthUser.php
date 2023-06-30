<?php

declare(strict_types=1);

namespace Acme\Domain\User;

final class AuthUser extends User
{
    public function __construct(
        UserId $userId,
        Username $username,
        Email $email,
        private readonly Password $password,
    ) {
        parent::__construct($userId, $username, $email);
    }

    public function password(): string
    {
        return $this->password->password;
    }
}
