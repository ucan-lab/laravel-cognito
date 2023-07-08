<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoType;

enum AuthFlow: string
{
    case USER_SRP_AUTH = 'USER_SRP_AUTH';
    case REFRESH_TOKEN_AUTH = 'REFRESH_TOKEN_AUTH';
    case REFRESH_TOKEN = 'REFRESH_TOKEN';
    case CUSTOM_AUTH = 'CUSTOM_AUTH';
    case ADMIN_NO_SRP_AUTH = 'ADMIN_NO_SRP_AUTH';
    case USER_PASSWORD_AUTH = 'USER_PASSWORD_AUTH';
    case ADMIN_USER_PASSWORD_AUTH = 'ADMIN_USER_PASSWORD_AUTH';
}
