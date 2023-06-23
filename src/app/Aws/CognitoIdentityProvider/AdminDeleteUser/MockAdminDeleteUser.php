<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminDeleteUser;

final class MockAdminDeleteUser implements AdminDeleteUser
{
    public function execute(AdminDeleteUserPayload $payload): AdminDeleteUserResult
    {
        return new AdminDeleteUserResult();
    }
}
