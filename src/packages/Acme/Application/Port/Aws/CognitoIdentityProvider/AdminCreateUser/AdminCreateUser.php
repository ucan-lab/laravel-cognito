<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminCreateUser;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserResult;

/**
 * 指定されたユーザー プールに新しいユーザーを作成します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admincreateuser
 */
interface AdminCreateUser
{
    public function execute(AdminCreateUserPayload $payload): AdminCreateUserResult;
}
