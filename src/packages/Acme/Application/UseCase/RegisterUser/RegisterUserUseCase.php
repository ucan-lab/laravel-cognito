<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

use Acme\Domain\User\UserFactory;
use Acme\Domain\User\UserRepository;
use Acme\Domain\User\UserService;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RegisterUserUseCase
{
    public function __construct(
        private UserFactory $userFactory,
        private UserService $userService,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(RegisterUserUseCaseInput $input): RegisterUserUseCaseOutput
    {
        $user = $this->userFactory->createForUser($input->username, $input->email, $input->password);

        DB::transaction(function () use ($user) {
            if ($this->userService->exists($user)) {
                throw new CannotRegisterUserException($user->username() . ' ユーザー名は既に存在しています。');
            }

            $this->userRepository->saveForUser($user);
        });

        return new RegisterUserUseCaseOutput(
            $user->userId(),
        );
    }
}
