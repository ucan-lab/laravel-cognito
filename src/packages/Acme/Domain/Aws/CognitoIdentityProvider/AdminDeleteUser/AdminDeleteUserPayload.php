<?php

declare(strict_types=1);

namespace Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser;

final readonly class AdminDeleteUserPayload
{
    /**
     * @param string $userPoolId 必須
     * @param string $username 必須
     */
    private function __construct(
        public string $userPoolId,
        public string $username,
    ) {
    }

    /**
     * @return static
     */
    public static function create(string $username): self
    {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
        );
    }
}
