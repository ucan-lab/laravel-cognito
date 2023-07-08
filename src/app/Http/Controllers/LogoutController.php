<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Application\Port\Aws\AdminUserGlobalSignOutPayload;
use Acme\Application\Port\Aws\CognitoClient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class LogoutController
{
    public function __construct(private CognitoClient $cognitoClient)
    {
    }

    public function __invoke(): RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $payload = AdminUserGlobalSignOutPayload::create($user->username);
            $this->cognitoClient->adminUserGlobalSignOut($payload);

            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
        }

        return redirect()->route('welcome');
    }
}
