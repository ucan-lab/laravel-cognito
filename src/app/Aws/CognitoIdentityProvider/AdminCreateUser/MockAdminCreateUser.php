<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminCreateUser;

use Carbon\Carbon;
use Illuminate\Support\Str;

final class MockAdminCreateUser implements AdminCreateUser
{
    public function execute(AdminCreateUserPayload $payload): AdminCreateUserResult
    {
        return AdminCreateUserResult::createForMock([
            'Attributes' => [
                [
                    'Name' => 'sub',
                    'Value' => (string) Str::uuid(),
                ],
            ],
            'Enabled' => true,
            'MFAOptions' => [],
            'UserCreateDate' => Carbon::now(),
            'UserLastModifiedDate' => Carbon::now(),
            'UserStatus' => 'FORCE_CHANGE_PASSWORD',
            'Username' => 'mock-user',
        ]);
    }
}
