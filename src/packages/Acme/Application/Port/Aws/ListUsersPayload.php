<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

final readonly class ListUsersPayload implements CognitoPayload
{
    private function __construct(
        public string $userPoolId,
        public string $paginationToken,
    ) {
    }

    /**
     * @return $this
     */
    public static function create(string $paginationToken = ''): self
    {
        return new self(
            config('services.cognito.user_pool_id'),
            $paginationToken,
        );
    }
}
