<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

interface CognitoClient
{
    public function adminCreateUser(AdminCreateUserPayload $payload): AdminCreateUserResult;
    public function adminDeleteUser(AdminDeleteUserPayload $payload): AdminDeleteUserResult;
    public function adminGetUser(AdminGetUserPayload $payload): AdminGetUserResult;
    public function adminInitiateAuth(AdminInitiateAuthPayload $payload): AdminInitiateAuthResult;
    public function adminRespondToAuthChallenge(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult;
    public function adminSetUserPassword(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult;
    public function adminUserGlobalSignOut(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult;
    public function listUsers(ListUsersPayload $payload): ListUsersResult;
}
