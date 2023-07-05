<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminGetUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserResult;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminGetUser implements AdminGetUser
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminGetUserPayload $payload): AdminGetUserResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $awsResult = $this->client->adminGetUser([
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = AdminGetUserResult::createForAws($awsResult);

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
