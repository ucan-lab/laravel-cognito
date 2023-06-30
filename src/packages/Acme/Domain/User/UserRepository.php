<?php

declare(strict_types=1);

namespace Acme\Domain\User;

interface UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function findByUsername(Username $username): User;

    public function saveForUser(User $user): void;
}
