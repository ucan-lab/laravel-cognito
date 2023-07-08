<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Application\Port\Aws\AdminInitiateAuthPayload;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Domain\User\UserRepository;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class LoginController
{
    public function __construct(
        private UserRepository $userRepository,
        private CognitoClient $cognitoClient,
    ) {
    }

    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $user = $this->userRepository->findByUsername($request->username());

        $payload = AdminInitiateAuthPayload::createForAdminUserPasswordAuthFlow($user->username(), $request->password());
        $this->cognitoClient->adminInitiateAuth($payload);

        Auth::loginUsingId($user->userId());
        Session::regenerate();
        Session::regenerateToken();

        return redirect()->route('dashboard');
    }
}
