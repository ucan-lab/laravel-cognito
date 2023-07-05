<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminRespondToAuthChallenge;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallenge;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallengePayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallengeResult;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsAdminRespondToAuthChallenge implements AdminRespondToAuthChallenge
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

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

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
