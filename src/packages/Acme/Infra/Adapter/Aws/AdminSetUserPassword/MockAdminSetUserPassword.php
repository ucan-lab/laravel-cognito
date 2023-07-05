<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminSetUserPassword;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPassword;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPasswordPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPasswordResult;

final class MockAdminSetUserPassword implements AdminSetUserPassword
{
    public function execute(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult
    {
        return new AdminSetUserPasswordResult();
    }
}
