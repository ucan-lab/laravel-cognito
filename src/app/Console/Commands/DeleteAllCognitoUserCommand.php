<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Acme\Application\Port\Aws\AdminDeleteUserPayload;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Application\Port\Aws\ListUsersPayload;
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

    public function handle(CognitoClient $cognitoClient): int
    {
        if (! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        $usernameList = $this->executeListUsers($cognitoClient);

        $this->withProgressBar($usernameList, fn (string $username) => $this->executeAdminDeleteUser($cognitoClient, $username));
        $this->newLine();

        return self::SUCCESS;
    }

    private function executeListUsers(CognitoClient $cognitoClient): array
    {
        $payload = ListUsersPayload::create();
        $result = $cognitoClient->listUsers($payload);

        if ($result->hasNext() === false) {
            return $result->usernameList;
        }

        $usernameList = [];
        $usernameList[] = $result->usernameList;

        do {
            $payload = ListUsersPayload::create($result->paginationToken);
            $result = $cognitoClient->listUsers($payload);
            $usernameList[] = $result->usernameList;
        } while ($result->hasNext());

        return Arr::flatten($usernameList);
    }

    private function executeAdminDeleteUser(CognitoClient $cognitoClient, string $username): void
    {
        $payload = AdminDeleteUserPayload::create($username);
        $cognitoClient->adminDeleteUser($payload);
    }
}
