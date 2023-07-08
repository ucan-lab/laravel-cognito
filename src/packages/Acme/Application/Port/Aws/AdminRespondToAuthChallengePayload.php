<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

use Acme\Application\Port\Aws\CognitoType\ChallengeName;
use Acme\Infra\Adapter\Aws\SecretHashGenerator;

final readonly class AdminRespondToAuthChallengePayload implements CognitoPayload
{
    private function __construct(
        public string $userPoolId,
        public string $clientId,
        public string $session,
        public ChallengeName $challengeName,
        public array $challengeResponses,
        public array $clientMetadata,
        public array $contextData,
        public array $analyticsMetadata,
    ) {
    }

    /**
     * @return static
     */
    public static function createForSms(
        string $session,
        string $username,
        string $code,
    ): self {
        return new self(
            config('services.cognito.user_pool_id'),
            config('services.cognito.app_client_id'),
            $session,
            ChallengeName::SMS_MFA,
            [
                'SMS_MFA_CODE' => $code,
                'USERNAME' => $username,
                'SECRET_HASH' => SecretHashGenerator::generate($username),
            ],
            [],
            [],
            [],
        );
    }

    /**
     * @return static
     */
    public static function createForSoftwareToken(
        string $session,
        string $username,
        string $code,
    ): self {
        return new self(
            config('services.cognito.user_pool_id'),
            config('services.cognito.app_client_id'),
            $session,
            ChallengeName::SOFTWARE_TOKEN_MFA,
            [
                'USERNAME' => $username,
                'SOFTWARE_TOKEN_MFA_CODE' => $code,
                'SECRET_HASH' => SecretHashGenerator::generate($username),
            ],
            [],
            [],
            [],
        );
    }
}
