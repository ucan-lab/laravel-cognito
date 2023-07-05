<?php

declare(strict_types=1);

namespace Acme\Infra\Repository\User;

use Acme\Domain\User\User;
use Acme\Domain\User\UserFactory;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserNotFoundException;
use Acme\Domain\User\UserRepository;
use App\Models\User as EloquentUser;

final readonly class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserFactory $userFactory,
    ) {
    }

    public function findByUsername(Username $username): User
    {
        $eloquentUser = EloquentUser::where('username', $username->username)->first();

        if ($eloquentUser === null) {
            throw new UserNotFoundException('存在しないユーザーです。');
        }

        return $this->userFactory->createForRepository($eloquentUser->uuid, $eloquentUser->username);
    }

    public function save(User $user): void
    {
        EloquentUser::create([
            'uuid' => $user->userId(),
            'username' => $user->username(),
        ]);
    }
}
