<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminDeleteUser;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserResult;

/**
 * 管理者としてユーザーを削除します。どのユーザーでも機能します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admindeleteuser
 */
interface AdminDeleteUser
{
    public function execute(AdminDeleteUserPayload $payload): AdminDeleteUserResult;
}
