<?php

declare(strict_types=1);

namespace Acme\Domain\User;

use Acme\Infra\Util\Uuid;

final class UserFactory
{
    public function createForUser(string $username, string $email, string $password): User
    {
        return new User(
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
            null,
        );
    }
}
