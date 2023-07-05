<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\ShowUserProfile;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserPayload;
use Acme\Domain\User\UserFactory;
use Acme\Domain\User\UserInconsistencyException;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserRepository;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

final readonly class ShowUserProfileUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private AdminGetUser $adminGetUser,
        private UserFactory $userFactory,
    ) {
    }

    public function show(ShowUserProfileUseCaseInput $input): ShowUserProfileUseCaseOutput
    {
        $username = new Username($input->username);
        $user = $this->userRepository->findByUsername($username);

        try {
            $payload = AdminGetUserPayload::create($username->username);
            $result = $this->adminGetUser->execute($payload);
        } catch (CognitoIdentityProviderException $exception) {
            if ($exception->getAwsErrorCode() === 'UserNotFoundException') {
                throw new UserInconsistencyException('存在しないユーザーです。');
            }

            throw $exception;
        }

        $user = $this->userFactory->createForAdminGetUser($user, $result);

        return new ShowUserProfileUseCaseOutput($user->username(), $user->email());
    }
}
