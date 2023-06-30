<?php

declare(strict_types=1);

namespace Acme\Domain\User;

class User
{
    public function __construct(
        protected UserId $userId,
        protected Username $username,
        protected Email $email,
    ) {
    }

    public function userId(): string
    {
        return $this->userId->userId;
    }

    public function username(): string
    {
        return $this->username->username;
    }

    public function usernameObject(): Username
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email->email;
    }
}
