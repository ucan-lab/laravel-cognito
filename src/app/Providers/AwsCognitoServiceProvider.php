<?php

declare(strict_types=1);

namespace App\Providers;

use App\Aws\CognitoIdentityProvider\CognitoIdentityProviderClientFactory;
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
            $this->app->singleton(CognitoIdentityProviderClient::class, fn () => CognitoIdentityProviderClientFactory::createForIamAuth());
        } else {
            $this->app->singleton(CognitoIdentityProviderClient::class, fn () => CognitoIdentityProviderClientFactory::createForCredentials());
        }

        if (config('services.cognito.enabled')) {
            // ...
        } else {
            // ...
        }
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CognitoIdentityProviderClient::class,
        ];
    }
}
