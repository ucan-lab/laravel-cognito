<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Application\Port\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOut;
use Acme\Domain\Aws\CognitoIdentityProvider\AdminUserGlobalSignOut\AdminUserGlobalSignOutPayload;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class LogoutController
{
    public function __construct(private AdminUserGlobalSignOut $adminUserGlobalSignOut)
    {
    }

    public function __invoke(): RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $payload = AdminUserGlobalSignOutPayload::create($user->username);
            $this->adminUserGlobalSignOut->execute($payload);

            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
        }

        return redirect()->route('welcome');
    }
}
