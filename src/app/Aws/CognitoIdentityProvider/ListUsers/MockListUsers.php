<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\ListUsers;

final class MockListUsers implements ListUsers
{
    public function __construct()
    {
    }

    public function execute(ListUsersPayload $payload): ListUsersResult
    {
        return ListUsersResult::createForLocal([]);
    }
}
