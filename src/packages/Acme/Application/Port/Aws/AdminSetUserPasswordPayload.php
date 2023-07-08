<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

final readonly class AdminSetUserPasswordPayload implements CognitoPayload
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
