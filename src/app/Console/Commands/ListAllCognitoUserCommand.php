<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Acme\Application\Port\Aws\CognitoClient;
use Acme\Application\Port\Aws\ListUsersPayload;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

final class ListAllCognitoUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list-all-cognito-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete All Cognito User';

    public function handle(CognitoClient $cognitoClient): int
    {
        $usernameList = $this->executeListUsers($cognitoClient);

        array_map(fn (string $username) => $this->info($username), $usernameList);

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
}
