<?php

declare(strict_types=1);

namespace App\Providers;

use Acme\Application\Port\Aws\CognitoClient;
use Acme\Infra\Adapter\Aws\AwsCognitoClient;
use Acme\Infra\Adapter\Aws\MockCognitoClient;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

final class AwsCognitoServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (config('services.cognito.use_iam_auth')) {
            $this->app->singleton(CognitoIdentityProviderClient::class, fn () => $this->createForIamAuth());
        } else {
            $this->app->singleton(CognitoIdentityProviderClient::class, fn () => $this->createForCredentials());
        }

        if (config('services.cognito.enabled')) {
            $this->app->singleton(CognitoClient::class, AwsCognitoClient::class);
        } else {
            $this->app->singleton(CognitoClient::class, MockCognitoClient::class);
        }
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CognitoIdentityProviderClient::class,
            CognitoClient::class,
        ];
    }

    private function createForIamAuth(): CognitoIdentityProviderClient
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

    private function createForCredentials(): CognitoIdentityProviderClient
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
