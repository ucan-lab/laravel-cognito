<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminInitiateAuth;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuthPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuthResult;

/**
 * 管理者として認証フローを開始します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admininitiateauth
 */
interface AdminInitiateAuth
{
    public function execute(AdminInitiateAuthPayload $payload): AdminInitiateAuthResult;
}
