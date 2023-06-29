<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge;

/**
 * 認証チャレンジに応答します。
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#adminrespondtoauthchallenge
 */
interface AdminRespondToAuthChallenge
{
    public function execute(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult;
}
