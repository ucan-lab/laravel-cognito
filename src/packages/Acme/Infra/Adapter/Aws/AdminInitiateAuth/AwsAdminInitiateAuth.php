<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminInitiateAuth;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuth;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuthPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuthResult;
use Acme\Domain\Aws\CognitoIdentityProvider\SecretHashGenerator;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminInitiateAuth implements AdminInitiateAuth
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminInitiateAuthPayload $payload): AdminInitiateAuthResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $secretHash = SecretHashGenerator::generate($payload->username);

        $awsResult = $this->client->adminInitiateAuth([
            'AnalyticsMetadata' => $payload->analyticsMetadata,
            'AuthFlow' => $payload->authFlow->value,
            'AuthParameters' => [
                'USERNAME' => $payload->username,
                'PASSWORD' => $payload->password,
                'SECRET_HASH' => $secretHash,
            ],
            'ClientId' => $payload->clientId,
            'ClientMetadata' => $payload->clientMetadata,
            // 'ContextData' => $payload->contextData ?? null, // 未使用
            'UserPoolId' => $payload->userPoolId,
        ]);

        $result = AdminInitiateAuthResult::createForAws($awsResult);

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
