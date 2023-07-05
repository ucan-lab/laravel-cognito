<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutResult;

/**
 * すべてのデバイスからユーザーをサインアウトします。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#adminuserglobalsignout
 */
interface AdminUserGlobalSignOut
{
    public function execute(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult;
}
