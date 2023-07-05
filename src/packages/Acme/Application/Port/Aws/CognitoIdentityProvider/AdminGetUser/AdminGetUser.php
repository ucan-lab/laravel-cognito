<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminGetUser;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserResult;

/**
 * ユーザープール内のユーザー名で指定されたユーザーを管理者として取得します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admingetuser
 */
interface AdminGetUser
{
    public function execute(AdminGetUserPayload $payload): AdminGetUserResult;
}
