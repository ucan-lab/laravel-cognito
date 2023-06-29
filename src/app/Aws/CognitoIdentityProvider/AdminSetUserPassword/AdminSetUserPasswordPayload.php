<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminSetUserPassword;

final readonly class AdminSetUserPasswordPayload
{
    private function __construct(
        public string $userPoolId,
        public string $username,
        public string $password,
        public bool $permanent,
    ) {
    }

    /**
     * @return static
     */
    public static function createForPermanent(string $username, string $password): self
    {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
            $password,
            true,
        );
    }

    /**
     * @return static
     */
    public static function createForTemporary(string $username, string $password): self
    {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
            $password,
            false,
        );
    }
}
