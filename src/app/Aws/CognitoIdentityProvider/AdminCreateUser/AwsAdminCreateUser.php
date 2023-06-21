<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminCreateUser;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminCreateUser implements AdminCreateUser
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminCreateUserPayload $payload): AdminCreateUserResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        $awsResult = $this->client->adminCreateUser([
            'ClientMetadata' => $payload->clientMetadata,
            'DesiredDeliveryMediums' => $payload->desiredDeliveryMediums,
            'ForceAliasCreation' => $payload->forceAliasCreation,
            'MessageAction' => $payload->messageAction,
            'TemporaryPassword' => $payload->temporaryPassword,
            'UserAttributes' => $payload->userAttributes->attributes,
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
            'ValidationData' => $payload->validationData,
        ]);

        $result = AdminCreateUserResult::createForAws($awsResult);

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
