<?php

declare(strict_types=1);

namespace Acme\Application\UseCase\ShowUserProfile;

final readonly class ShowUserProfileUseCaseInput
{
    public function __construct(public string $username)
    {
    }
}
