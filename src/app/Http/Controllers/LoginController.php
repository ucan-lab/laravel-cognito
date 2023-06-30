<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Domain\User\Username;
use Acme\Domain\User\UserRepository;
use App\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuth;
use App\Aws\CognitoIdentityProvider\AdminInitiateAuth\AdminInitiateAuthPayload;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class LoginController
{
    public function __construct(
        private UserRepository $userRepository,
        private AdminInitiateAuth $adminInitiateAuth,
    ) {
    }

    public function __invoke(LoginRequest $request, AdminInitiateAuth $adminInitiateAuth): RedirectResponse
    {
        $user = $this->userRepository->findByUsername(new Username($request->username()));

        $payload = AdminInitiateAuthPayload::createForAdminUserPasswordAuthFlow($user->username(), $request->password());
        $this->adminInitiateAuth->execute($payload);

        Auth::loginUsingId($user->userId());
        Session::regenerate();
        Session::regenerateToken();

        return redirect()->route('dashboard');
    }
}
