<?php

declare(strict_types=1);

namespace App\Providers;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUser;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuth;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge\AdminRespondToAuthChallenge;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminSetUserPassword\AdminSetUserPassword;
use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOut;
use Acme\Application\Port\Aws\CognitoIdentityProvider\ListUsers\ListUsers;
use Acme\Infra\Adapter\Aws\AdminCreateUser\AwsAdminCreateUser;
use Acme\Infra\Adapter\Aws\AdminCreateUser\MockAdminCreateUser;
use Acme\Infra\Adapter\Aws\AdminDeleteUser\AwsAdminDeleteUser;
use Acme\Infra\Adapter\Aws\AdminDeleteUser\MockAdminDeleteUser;
use Acme\Infra\Adapter\Aws\AdminGetUser\AwsAdminGetUser;
use Acme\Infra\Adapter\Aws\AdminGetUser\MockAdminGetUser;
use Acme\Infra\Adapter\Aws\AdminInitiateAuth\AwsAdminInitiateAuth;
use Acme\Infra\Adapter\Aws\AdminInitiateAuth\MockAdminInitiateAuth;
use Acme\Infra\Adapter\Aws\AdminRespondToAuthChallenge\AwsAdminRespondToAuthChallenge;
use Acme\Infra\Adapter\Aws\AdminRespondToAuthChallenge\MockAdminRespondToAuthChallenge;
use Acme\Infra\Adapter\Aws\AdminSetUserPassword\AwsAdminSetUserPassword;
use Acme\Infra\Adapter\Aws\AdminSetUserPassword\MockAdminSetUserPassword;
use Acme\Infra\Adapter\Aws\AdminUserGlobalSignOut\AwsAdminUserGlobalSignOut;
use Acme\Infra\Adapter\Aws\AdminUserGlobalSignOut\MockAdminUserGlobalSignOut;
use Acme\Infra\Adapter\Aws\ListUsers\AwsListUsers;
use Acme\Infra\Adapter\Aws\ListUsers\MockListUsers;
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
            $this->app->bind(AdminCreateUser::class, AwsAdminCreateUser::class);
            $this->app->bind(AdminDeleteUser::class, AwsAdminDeleteUser::class);
            $this->app->bind(AdminGetUser::class, AwsAdminGetUser::class);
            $this->app->bind(AdminInitiateAuth::class, AwsAdminInitiateAuth::class);
            $this->app->bind(AdminRespondToAuthChallenge::class, AwsAdminRespondToAuthChallenge::class);
            $this->app->bind(AdminSetUserPassword::class, AwsAdminSetUserPassword::class);
            $this->app->bind(AdminUserGlobalSignOut::class, AwsAdminUserGlobalSignOut::class);
            $this->app->bind(ListUsers::class, AwsListUsers::class);
        } else {
            $this->app->bind(AdminCreateUser::class, MockAdminCreateUser::class);
            $this->app->bind(AdminDeleteUser::class, MockAdminDeleteUser::class);
            $this->app->bind(AdminGetUser::class, MockAdminGetUser::class);
            $this->app->bind(AdminInitiateAuth::class, MockAdminInitiateAuth::class);
            $this->app->bind(AdminRespondToAuthChallenge::class, MockAdminRespondToAuthChallenge::class);
            $this->app->bind(AdminSetUserPassword::class, MockAdminSetUserPassword::class);
            $this->app->bind(AdminUserGlobalSignOut::class, MockAdminUserGlobalSignOut::class);
            $this->app->bind(ListUsers::class, MockListUsers::class);
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
            AdminRespondToAuthChallenge::class,
            AdminSetUserPassword::class,
            AdminUserGlobalSignOut::class,
            ListUsers::class,
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
