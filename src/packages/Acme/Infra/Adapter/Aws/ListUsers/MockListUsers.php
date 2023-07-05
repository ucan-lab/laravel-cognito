<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\ListUsers;

use Acme\Application\Port\Aws\CognitoIdentityProvider\ListUsers\ListUsers;
use Acme\Domain\Aws\CognitoIdentityProvider\ListUsers\ListUsersPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\ListUsers\ListUsersResult;

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
