<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminDeleteUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserResult;

final class MockAdminDeleteUser implements AdminDeleteUser
{
    public function execute(AdminDeleteUserPayload $payload): AdminDeleteUserResult
    {
        return new AdminDeleteUserResult();
    }
}
