<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\ShowUserProfile;

use Acme\Domain\User\UserRepository;

final readonly class ShowUserProfileUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function show(ShowUserProfileUseCaseInput $input): ShowUserProfileUseCaseOutput
    {
        $user = $this->userRepository->findByUsername($input->username);

        return new ShowUserProfileUseCaseOutput($user->username(), $user->email());
    }
}
