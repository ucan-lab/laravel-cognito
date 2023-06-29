<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminSetUserPassword;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminSetUserPassword implements AdminSetUserPassword
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $this->client->adminSetUserPassword([
            'Password' => $payload->password,
            'Permanent' => $payload->permanent,
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = new AdminSetUserPasswordResult();

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
