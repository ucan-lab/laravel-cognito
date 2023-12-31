<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoType;

enum UserMFASetting: string
{
    case SMS_MFA = 'SMS_MFA';
    case SOFTWARE_TOKEN_MFA = 'SOFTWARE_TOKEN_MFA'; // TOTP
}
