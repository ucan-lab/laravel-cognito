<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminSetUserPassword;

final class MockAdminSetUserPassword implements AdminSetUserPassword
{
    public function execute(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult
    {
        return new AdminSetUserPasswordResult();
    }
}
