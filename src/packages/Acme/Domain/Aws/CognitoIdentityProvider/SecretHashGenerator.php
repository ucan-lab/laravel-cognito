<?php

declare(strict_types=1);

namespace Acme\Domain\Aws\CognitoIdentityProvider;

/**
 * シークレットハッシュ値の計算
 *
 * @link https://docs.aws.amazon.com/cognito/latest/developerguide/signing-up-users-in-your-app.html#cognito-user-pools-computing-secret-hash
 */
final class SecretHashGenerator
{
    public static function generate(string $username): string
    {
        $data = $username . config('services.cognito.app_client_id');
        $key = config('services.cognito.app_client_secret');

        $hash = hash_hmac(
            'sha256',
            $data,
            $key,
            true
        );

        return base64_encode($hash);
    }
}
