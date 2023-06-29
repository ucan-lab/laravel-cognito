<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\ListUsers;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Log;
use JsonException;

final readonly class AwsListUsers implements ListUsers
{
    public function __construct(private CognitoIdentityProviderClient $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function execute(ListUsersPayload $payload): ListUsersResult
    {
        Log::debug(get_class($payload), json_decode(json_encode($payload, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

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

        Log::debug(get_class($result), json_decode(json_encode($result, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));

        return $result;
    }
}
