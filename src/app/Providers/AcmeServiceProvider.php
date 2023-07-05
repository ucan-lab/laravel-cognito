<?php

declare(strict_types=1);

namespace App\Providers;

use Acme\Domain\User\UserRepository;
use Acme\Infra\Repository\User\EloquentUserRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

final class AcmeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            UserRepository::class,
        ];
    }
}
