<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminUserGlobalSignOut;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOut;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutResult;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminUserGlobalSignOut implements AdminUserGlobalSignOut
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $this->client->adminUserGlobalSignOut([
            'UserPoolId' => $payload->userPoolId, // REQUIRED
            'Username' => $payload->username, // REQUIRED
        ]);

        $result = new AdminUserGlobalSignOutResult();

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
