<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut;

/**
 * すべてのデバイスからユーザーをサインアウトします。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#adminuserglobalsignout
 */
interface AdminUserGlobalSignOut
{
    public function execute(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult;
}
