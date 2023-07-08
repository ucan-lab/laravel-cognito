<?php

declare(strict_types=1);

namespace Acme\Domain\User;

interface UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function findByUsername(string $username): User;

    public function save(User $user): void;
}
