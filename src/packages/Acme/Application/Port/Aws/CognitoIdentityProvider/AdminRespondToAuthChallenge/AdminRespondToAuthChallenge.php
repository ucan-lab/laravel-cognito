<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge;

use Acme\Domain\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallengePayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallengeResult;

/**
 * 認証チャレンジに応答します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#adminrespondtoauthchallenge
 */
interface AdminRespondToAuthChallenge
{
    public function execute(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult;
}
