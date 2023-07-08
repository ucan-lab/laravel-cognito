<?php

declare(strict_types=1);

namespace Acme\Domain\User;

final readonly class UserService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function exists(User $user): bool
    {
        try {
            $this->userRepository->findByUsername($user->username());
        } catch (UserNotFoundException $exception) {
            if ($exception instanceof UserInconsistencyException) {
                throw $exception;
            }

            return false;
        }

        return true;
    }
}
