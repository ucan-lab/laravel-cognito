<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut;

final class MockAdminUserGlobalSignOut implements AdminUserGlobalSignOut
{
    public function execute(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult
    {
        return new AdminUserGlobalSignOutResult();
    }
}
