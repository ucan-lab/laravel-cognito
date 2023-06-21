<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

/**
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.CognitoIdentityProvider.CognitoIdentityProviderClient.html
 */
final class CognitoIdentityProviderClientFactory
{
    public static function createForIamAuth(): CognitoIdentityProviderClient
    {
        return new CognitoIdentityProviderClient([
            'version' => config('services.cognito.version'),
            'region' => config('services.cognito.region'),
            'http' => [
                'connect_timeout' => config('services.cognito.http.connect_timeout'),
                'timeout' => config('services.cognito.http.timeout'),
            ],
        ]);
    }

    public static function createForCredentials(): CognitoIdentityProviderClient
    {
        return new CognitoIdentityProviderClient([
            'credentials' => [
                'key' => config('services.cognito.key'),
                'secret' => config('services.cognito.secret'),
            ],
            'version' => config('services.cognito.version'),
            'region' => config('services.cognito.region'),
            'http' => [
                'connect_timeout' => config('services.cognito.http.connect_timeout'),
                'timeout' => config('services.cognito.http.timeout'),
            ],
        ]);
    }
}
