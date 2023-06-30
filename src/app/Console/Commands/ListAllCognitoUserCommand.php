<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Aws\CognitoIdentityProvider\ListUsers\ListUsers;
use App\Aws\CognitoIdentityProvider\ListUsers\ListUsersPayload;
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

    public function handle(ListUsers $listUsers): int
    {
        $usernameList = $this->executeListUsers($listUsers);

        array_map(fn (string $username) => $this->info($username), $usernameList);

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
}
