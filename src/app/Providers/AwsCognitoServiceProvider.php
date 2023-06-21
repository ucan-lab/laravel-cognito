<?php

declare(strict_types=1);

namespace App\Providers;

use App\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\AwsAdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\MockAdminCreateUser;
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
            $this->app->bind(AdminCreateUser::class, AwsAdminCreateUser::class);
        } else {
            $this->app->bind(AdminCreateUser::class, MockAdminCreateUser::class);
        }
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CognitoIdentityProviderClient::class,
            AdminCreateUser::class,
        ];
    }
}
