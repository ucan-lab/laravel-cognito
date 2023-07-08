<?php

declare(strict_types=1);

namespace Acme\Infra\Repository\User;

use Acme\Domain\User\User;
use Acme\Domain\User\UserId;
use Acme\Domain\User\Username;
use Acme\Domain\User\UserNotFoundException;
use Acme\Domain\User\UserRepository;

final class MockUserRepository implements UserRepository
{
    private array $users;

    public function __construct()
    {
        $this->users = [
            'ok-user1' => $this->createUser('12345678-1234-1234-1234-000000000001', 'ok-user1'),
            'ok-user2' => $this->createUser('12345678-1234-1234-1234-000000000002', 'ok-user2'),
            'ok-user3' => $this->createUser('12345678-1234-1234-1234-000000000003', 'ok-user3'),
            'ok-user4' => $this->createUser('12345678-1234-1234-1234-000000000004', 'ok-user4'),
            'ok-user5' => $this->createUser('12345678-1234-1234-1234-000000000005', 'ok-user5'),
            'ok-user6' => $this->createUser('12345678-1234-1234-1234-000000000006', 'ok-user6'),
            'ok-user7' => $this->createUser('12345678-1234-1234-1234-000000000007', 'ok-user7'),
            'ok-user8' => $this->createUser('12345678-1234-1234-1234-000000000008', 'ok-user8'),
            'ok-user9' => $this->createUser('12345678-1234-1234-1234-000000000009', 'ok-user9'),
        ];
    }

    public function findByUsername(string $username): User
    {
        return $this->users[$username] ?? throw new UserNotFoundException('存在しないユーザーです。');
    }

    public function save(User $user): void
    {
        $this->users[$user->username()] = $user;
    }

    private function createUser(string $userId, string $username): User
    {
        return new User(
            new UserId($userId),
            new Username($username),
            null,
            null,
        );
    }
}
