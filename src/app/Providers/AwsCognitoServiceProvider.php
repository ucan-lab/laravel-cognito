<?php

declare(strict_types=1);

namespace App\Providers;

use App\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\AwsAdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminCreateUser\MockAdminCreateUser;
use App\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUser;
use App\Aws\CognitoIdentityProvider\AdminDeleteUser\AwsAdminDeleteUser;
use App\Aws\CognitoIdentityProvider\AdminDeleteUser\MockAdminDeleteUser;
use App\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use App\Aws\CognitoIdentityProvider\AdminGetUser\AwsAdminGetUser;
use App\Aws\CognitoIdentityProvider\AdminGetUser\MockAdminGetUser;
use App\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuth;
use App\Aws\CognitoIdentityProvider\AdminInitiateAuth\AwsAdminInitiateAuth;
use App\Aws\CognitoIdentityProvider\AdminInitiateAuth\MockAdminInitiateAuth;
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
            $this->app->bind(AdminDeleteUser::class, AwsAdminDeleteUser::class);
            $this->app->bind(AdminGetUser::class, AwsAdminGetUser::class);
            $this->app->bind(AdminInitiateAuth::class, AwsAdminInitiateAuth::class);
        } else {
            $this->app->bind(AdminCreateUser::class, MockAdminCreateUser::class);
            $this->app->bind(AdminDeleteUser::class, MockAdminDeleteUser::class);
            $this->app->bind(AdminGetUser::class, MockAdminGetUser::class);
            $this->app->bind(AdminInitiateAuth::class, MockAdminInitiateAuth::class);
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
            AdminDeleteUser::class,
            AdminGetUser::class,
            AdminInitiateAuth::class,
        ];
    }
}
