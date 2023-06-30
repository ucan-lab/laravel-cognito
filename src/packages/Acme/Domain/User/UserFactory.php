<?php

declare(strict_types=1);

namespace Acme\Domain\User;

use Acme\Lang\Uuid;

final class UserFactory
{
    public function createForAuthUser(string $username, string $email, string $password): AuthUser
    {
        return new AuthUser(
            new UserId(Uuid::generate()),
            new Username($username),
            new Email($email),
            new Password($password),
        );
    }

    public function createForRepository(string $userId, string $username, string $email): User
    {
        return new User(
            new UserId($userId),
            new Username($username),
            new Email($email),
        );
    }
}
