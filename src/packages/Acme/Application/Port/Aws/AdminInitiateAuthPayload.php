<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

use Acme\Application\Port\Aws\CognitoType\AuthFlow;

final readonly class AdminInitiateAuthPayload implements CognitoPayload
{
    private function __construct(
        public string $clientId,
        public string $userPoolId,
        public AuthFlow $authFlow,
        public string $username,
        public string $password,
        public array $analyticsMetadata,
        public array $clientMetadata,
        public array $contextData,
    ) {
    }

    /**
     * @return static
     */
    public static function createForAdminUserPasswordAuthFlow(string $username, string $password): self
    {
        return new self(
            config('services.cognito.app_client_id'),
            config('services.cognito.user_pool_id'),
            AuthFlow::ADMIN_USER_PASSWORD_AUTH,
            $username,
            $password,
            [],
            [],
            [],
        );
    }
}
