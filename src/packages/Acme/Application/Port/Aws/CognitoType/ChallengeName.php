<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoType;

enum ChallengeName: string
{
    case SMS_MFA = 'SMS_MFA';
    case SOFTWARE_TOKEN_MFA = 'SOFTWARE_TOKEN_MFA';
    case SELECT_MFA_TYPE = 'SELECT_MFA_TYPE';
    case MFA_SETUP = 'MFA_SETUP';
    case PASSWORD_VERIFIER = 'PASSWORD_VERIFIER';
    case CUSTOM_CHALLENGE = 'CUSTOM_CHALLENGE';
    case DEVICE_SRP_AUTH = 'DEVICE_SRP_AUTH';
    case DEVICE_PASSWORD_VERIFIER = 'DEVICE_PASSWORD_VERIFIER';
    case ADMIN_NO_SRP_AUTH = 'ADMIN_NO_SRP_AUTH';
    case NEW_PASSWORD_REQUIRED = 'NEW_PASSWORD_REQUIRED';
}
