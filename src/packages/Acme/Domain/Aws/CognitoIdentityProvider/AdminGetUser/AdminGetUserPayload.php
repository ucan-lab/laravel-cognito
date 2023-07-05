<?php

declare(strict_types=1);

namespace Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser;

final readonly class AdminGetUserPayload
{
    private function __construct(
        public string $userPoolId,
        public string $username,
    ) {
    }

    /**
     * @return $this
     */
    public static function create(string $username): self
    {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
        );
    }
}
