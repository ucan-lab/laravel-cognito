<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminUserGlobalSignOut;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOut;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutResult;

final class MockAdminUserGlobalSignOut implements AdminUserGlobalSignOut
{
    public function execute(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult
    {
        return new AdminUserGlobalSignOutResult();
    }
}
