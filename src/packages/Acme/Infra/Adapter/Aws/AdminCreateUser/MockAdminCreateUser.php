<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws\AdminCreateUser;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUser;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser\AdminCreateUserResult;
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
