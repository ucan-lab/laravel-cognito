<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminDeleteUser;

/**
 * 管理者としてユーザーを削除します。どのユーザーでも機能します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admindeleteuser
 */
interface AdminDeleteUser
{
    public function execute(AdminDeleteUserPayload $payload): AdminDeleteUserResult;
}
