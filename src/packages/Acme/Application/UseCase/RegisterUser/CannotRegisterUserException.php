<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\RegisterUser;

use RuntimeException;

final class CannotRegisterUserException extends RuntimeException
{
}
