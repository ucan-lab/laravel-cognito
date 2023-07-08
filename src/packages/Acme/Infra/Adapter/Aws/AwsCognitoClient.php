<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws;

use Acme\Application\Port\Aws\AdminCreateUserPayload;
use Acme\Application\Port\Aws\AdminCreateUserResult;
use Acme\Application\Port\Aws\AdminDeleteUserPayload;
use Acme\Application\Port\Aws\AdminDeleteUserResult;
use Acme\Application\Port\Aws\AdminGetUserPayload;
use Acme\Application\Port\Aws\AdminGetUserResult;
use Acme\Application\Port\Aws\AdminInitiateAuthPayload;
use Acme\Application\Port\Aws\AdminInitiateAuthResult;
use Acme\Application\Port\Aws\AdminRespondToAuthChallengePayload;
use Acme\Application\Port\Aws\AdminRespondToAuthChallengeResult;
use Acme\Application\Port\Aws\AdminSetUserPasswordPayload;
use Acme\Application\Port\Aws\AdminSetUserPasswordResult;
use Acme\Application\Port\Aws\AdminUserGlobalSignOutPayload;
use Acme\Application\Port\Aws\AdminUserGlobalSignOutResult;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Application\Port\Aws\ListUsersPayload;
use Acme\Application\Port\Aws\ListUsersResult;
use Acme\Application\Port\Aws\CognitoPayload;
use Acme\Application\Port\Aws\CognitoResult;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsCognitoClient implements CognitoClient
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function adminCreateUser(AdminCreateUserPayload $payload): AdminCreateUserResult
    {
        $this->writePayloadLog($payload);

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

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminDeleteUser(AdminDeleteUserPayload $payload): AdminDeleteUserResult
    {
        $this->writePayloadLog($payload);

        $this->client->adminDeleteUser([
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = new AdminDeleteUserResult();

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminGetUser(AdminGetUserPayload $payload): AdminGetUserResult
    {
        $this->writePayloadLog($payload);

        $awsResult = $this->client->adminGetUser([
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = AdminGetUserResult::createForAws($awsResult);

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminInitiateAuth(AdminInitiateAuthPayload $payload): AdminInitiateAuthResult
    {
        $this->writePayloadLog($payload);

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

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminRespondToAuthChallenge(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult
    {
        $this->writePayloadLog($payload);

        $awsResult = $this->client->adminRespondToAuthChallenge([
            // 'AnalyticsMetadata' => [
            //     'AnalyticsEndpointId' => '<string>',
            // ],
            'ChallengeName' => $payload->challengeName, // REQUIRED
            'ChallengeResponses' => $payload->challengeResponses,
            'ClientId' => $payload->clientId, // REQUIRED
            // 'ClientMetadata' => ['<string>', ...],
            // 'ContextData' => [
            //     'EncodedData' => '<string>',
            //     'HttpHeaders' => [ // REQUIRED
            //         [
            //             'headerName' => '<string>',
            //             'headerValue' => '<string>',
            //         ],
            //         // ...
            //     ],
            //     'IpAddress' => '<string>', // REQUIRED
            //     'ServerName' => '<string>', // REQUIRED
            //     'ServerPath' => '<string>', // REQUIRED
            // ],
            'Session' => $payload->session,
            'UserPoolId' => $payload->userPoolId, // REQUIRED
        ]);

        $result = AdminRespondToAuthChallengeResult::createForAws($awsResult);

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminSetUserPassword(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult
    {
        $this->writePayloadLog($payload);

        $this->client->adminSetUserPassword([
            'Password' => $payload->password,
            'Permanent' => $payload->permanent,
            'UserPoolId' => $payload->userPoolId,
            'Username' => $payload->username,
        ]);

        $result = new AdminSetUserPasswordResult();

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function adminUserGlobalSignOut(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult
    {
        $this->writePayloadLog($payload);

        $this->client->adminUserGlobalSignOut([
            'UserPoolId' => $payload->userPoolId, // REQUIRED
            'Username' => $payload->username, // REQUIRED
        ]);

        $result = new AdminUserGlobalSignOutResult();

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function listUsers(ListUsersPayload $payload): ListUsersResult
    {
        $this->writePayloadLog($payload);

        if ($payload->paginationToken) {
            $awsResult = $this->client->listUsers([
                'UserPoolId' => $payload->userPoolId,
                // 'AttributesToGet' => ['<string>', ...],
                // 'Filter' => '<string>',
                // 'Limit' => <integer>,
                'PaginationToken' => $payload->paginationToken,
            ]);
        } else {
            $awsResult = $this->client->listUsers([
                'UserPoolId' => $payload->userPoolId,
                // 'AttributesToGet' => ['<string>', ...],
                // 'Filter' => '<string>',
                // 'Limit' => <integer>,
            ]);
        }

        $result = ListUsersResult::createForAws($awsResult);

        $this->writeResultLog($result);

        return $result;
    }

    /**
     * @param CognitoPayload $payload
     * @return void
     * @throws JsonException
     */
    private function writePayloadLog(CognitoPayload $payload): void
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @param CognitoResult $result
     * @return void
     * @throws JsonException
     */
    private function writeResultLog(CognitoResult $result): void
    {
        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));
    }
}
