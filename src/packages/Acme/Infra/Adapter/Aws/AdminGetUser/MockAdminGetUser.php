<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminGetUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminGetUser\AdminGetUserResult;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class MockAdminGetUser implements AdminGetUser
{
    public function execute(AdminGetUserPayload $payload): AdminGetUserResult
    {
        return AdminGetUserResult::createForMock([
            'Enabled' => true,
            'MFAOptions' => [],
            'PreferredMfaSetting' => null,
            'UserAttributes' => [
                [
                    'Name' => 'sub',
                    'Value' => (string) Str::uuid(),
                ],
                [
                    'Name' => 'email',
                    'Value' => $payload->username . '@example.com',
                ],
            ],
            'UserCreateDate' => Carbon::now(),
            'UserLastModifiedDate' => Carbon::now(),
            'UserMFASettingList' => [],
            'UserStatus' => 'FORCE_CHANGE_PASSWORD',
            'Username' => $payload->username,
        ]);
    }
}
