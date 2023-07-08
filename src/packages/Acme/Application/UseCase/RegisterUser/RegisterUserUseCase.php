<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

use Acme\Application\Port\Aws\AdminCreateUserPayload;
use Acme\Application\Port\Aws\AdminSetUserPasswordPayload;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Domain\User\Email;
use Acme\Domain\User\Password;
use Acme\Domain\User\User;
use Acme\Domain\User\UserId;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserRepository;
use Acme\Domain\User\UserService;
use Acme\Infra\Repository\Uuid;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class RegisterUserUseCase
{
    public function __construct(
        private UserService $userService,
        private UserRepository $userRepository,
        private CognitoClient $cognitoClient,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(RegisterUserUseCaseInput $input): RegisterUserUseCaseOutput
    {
        $user = new User(
            new UserId(Uuid::generate()),
            new Username($input->username),
            new Email($input->email),
            new Password($input->password),
        );

        DB::transaction(function () use ($user) {
            if ($this->userService->exists($user)) {
                throw new CannotRegisterUserException($user->username() . ' ユーザー名は既に存在しています。');
            }

            $this->userRepository->save($user);

            $payload = AdminCreateUserPayload::create($user->username(), $user->email());
            $this->cognitoClient->adminCreateUser($payload);

            // ランダムなパスワードでユーザーが作成されるため、パスワードを指定している
            $payload = AdminSetUserPasswordPayload::createForPermanent($user->username(), $user->password());
            $this->cognitoClient->adminSetUserPassword($payload);
        });

        return new RegisterUserUseCaseOutput(
            $user->userId(),
        );
    }
}
