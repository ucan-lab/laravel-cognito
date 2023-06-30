<?php

declare(strict_types=1);

namespace Acme\Infra\User;

use Acme\Domain\User\User;
use Acme\Domain\User\UserFactory;
use Acme\Domain\User\UserInconsistencyException;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserNotFoundException;
use Acme\Domain\User\UserRepository;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserPayload;
use App\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use App\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserPayload;
use App\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPassword;
use App\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPasswordPayload;
use App\Models\User as EloquentUser;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

final readonly class ConcreteUserRepository implements UserRepository
{
    public function __construct(
        private UserFactory $userFactory,
        private AdminGetUser $adminGetUser,
        private AdminCreateUser $adminCreateUser,
        private AdminSetUserPassword $adminSetUserPassword,
    ) {
    }

    public function findByUsername(Username $username): User
    {
        $eloquentUser = EloquentUser::where('username', $username->username)->first();

        if ($eloquentUser === null) {
            throw new UserNotFoundException('存在しないユーザーです。');
        }

        $payload = AdminGetUserPayload::create($username->username);

        try {
            $result = $this->adminGetUser->execute($payload);
        } catch (CognitoIdentityProviderException $exception) {
            if ($exception->getAwsErrorCode() === 'UserNotFoundException') {
                throw new UserInconsistencyException('存在しないユーザーです。');
            }

            throw $exception;
        }

        return $this->userFactory->createForRepository($eloquentUser->uuid, $eloquentUser->username, $result->email());
    }

    public function saveForUser(User $user): void
    {
        EloquentUser::create([
            'uuid' => $user->userId(),
            'username' => $user->username(),
        ]);

        $payload = AdminCreateUserPayload::create($user->username(), $user->email());
        $this->adminCreateUser->execute($payload);

        // ランダムなパスワードでユーザーが作成されるため、パスワードを指定している
        $payload = AdminSetUserPasswordPayload::createForPermanent($user->username(), $user->password());
        $this->adminSetUserPassword->execute($payload);
    }
}
