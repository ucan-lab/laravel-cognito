<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminGetUser;

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
