<?php

declare(strict_types=1);

namespace Acme\Application\ShowUserProfile;

use Acme\Domain\User\Username;
use Acme\Domain\User\UserRepository;

final class ShowUserProfileUseCase
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function show(ShowUserProfileUseCaseInput $input): ShowUserProfileUseCaseOutput
    {
        $username = new Username($input->username);
        $authUser = $this->userRepository->findByUsername($username);

        return new ShowUserProfileUseCaseOutput($authUser->username(), $authUser->email());
    }
}
