<?php

declare(strict_types=1);

namespace Acme\Application\RegisterUser;

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
        $authUser = $this->userFactory->createForAuthUser($input->username, $input->email, $input->password);

        DB::transaction(function () use ($authUser) {
            if ($this->userService->exists($authUser)) {
                throw new CannotRegisterUserException($authUser->username() . ' ユーザー名は既に存在しています。');
            }

            $this->userRepository->saveForAuthUser($authUser);
        });

        return new RegisterUserUseCaseOutput(
            $authUser->userId(),
        );
    }
}
