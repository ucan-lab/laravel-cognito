<?php

declare(strict_types=1);

namespace Acme\Infra\Repository\User;

use Acme\Application\Port\Aws\AdminGetUserPayload;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Domain\User\Email;
use Acme\Domain\User\User;
use Acme\Domain\User\UserId;
use Acme\Domain\User\UserInconsistencyException;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserNotFoundException;
use Acme\Domain\User\UserRepository;
use App\Models\User as EloquentUser;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

final readonly class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private CognitoClient $cognitoClient,
    ) {
    }

    public function findByUsername(string $username): User
    {
        $eloquentUser = EloquentUser::where('username', $username)->first();

        if ($eloquentUser === null) {
            throw new UserNotFoundException('存在しないユーザーです。');
        }

        try {
            $payload = AdminGetUserPayload::create($username);
            $result = $this->cognitoClient->adminGetUser($payload);
        } catch (CognitoIdentityProviderException $exception) {
            if ($exception->getAwsErrorCode() === 'UserNotFoundException') {
                throw new UserInconsistencyException('存在しないユーザーです。');
            }

            throw $exception;
        }

        return new User(
            new UserId($eloquentUser->uuid),
            new Username($eloquentUser->username),
            new Email($result->email()),
            null,
        );
    }

    public function save(User $user): void
    {
        EloquentUser::create([
            'uuid' => $user->userId(),
            'username' => $user->username(),
        ]);
    }
}
