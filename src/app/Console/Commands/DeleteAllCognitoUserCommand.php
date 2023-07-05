<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUser;
use Acme\Application\Port\Aws\CognitoIdentityProvider\ListUsers\ListUsers;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminDeleteUser\AdminDeleteUserPayload;
use Acme\Domain\Aws\CognitoIdentityProvider\ListUsers\ListUsersPayload;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;

final class DeleteAllCognitoUserCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-all-cognito-user
        {--force : Force the operation to run when in production}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete All Cognito User';

    public function handle(ListUsers $listUsers, AdminDeleteUser $adminDeleteUser): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $usernameList = $this->executeListUsers($listUsers);

        $this->withProgressBar($usernameList, fn (string $username) => $this->executeAdminDeleteUser($adminDeleteUser, $username));
        $this->newLine();

        return self::SUCCESS;
    }

    private function executeListUsers(ListUsers $listUsers): array
    {
        $payload = ListUsersPayload::create();
        $result = $listUsers->execute($payload);

        if ($result->hasNext() === false) {
            return $result->usernameList;
        }

        $usernameList = [];
        $usernameList[] = $result->usernameList;

        do {
            $payload = ListUsersPayload::create($result->paginationToken);
            $result = $listUsers->execute($payload);
            $usernameList[] = $result->usernameList;
        } while ($result->hasNext());

        return Arr::flatten($usernameList);
    }

    private function executeAdminDeleteUser(AdminDeleteUser $adminDeleteUser, string $username): void
    {
        $payload = AdminDeleteUserPayload::create($username);
        $adminDeleteUser->execute($payload);
    }
}
