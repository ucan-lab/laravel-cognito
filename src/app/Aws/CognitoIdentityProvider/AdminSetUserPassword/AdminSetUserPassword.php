<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminSetUserPassword;

/**
 * 指定されたユーザーのパスワードをユーザープールに管理者として設定します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#adminsetuserpassword
 */
interface AdminSetUserPassword
{
    public function execute(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult;
}
