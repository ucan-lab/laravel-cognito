<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminDeleteUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserResult;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminDeleteUser implements AdminDeleteUser
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminDeleteUserPayload $payload): AdminDeleteUserResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $this->client->adminDeleteUser([
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = new AdminDeleteUserResult();

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
