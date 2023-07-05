<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPassword;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPasswordPayload;
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
        private AdminCreateUser $adminCreateUser,
        private AdminSetUserPassword $adminSetUserPassword,
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

            $this->userRepository->save($user);

            $payload = AdminCreateUserPayload::create($user->username(), $user->email());
            $this->adminCreateUser->execute($payload);

            // ランダムなパスワードでユーザーが作成されるため、パスワードを指定している
            $payload = AdminSetUserPasswordPayload::createForPermanent($user->username(), $user->password());
            $this->adminSetUserPassword->execute($payload);
        });

        return new RegisterUserUseCaseOutput(
            $user->userId(),
        );
    }
}
