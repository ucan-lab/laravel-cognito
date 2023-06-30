<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Application\RegisterUser\CannotRegisterUserException;
use Acme\Application\RegisterUser\RegisterUserUseCase;
use Acme\Application\RegisterUser\RegisterUserUseCaseInput;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Throwable;

final class RegisterController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(RegisterRequest $request, RegisterUserUseCase $registerUser): RedirectResponse
    {
        $input = new RegisterUserUseCaseInput($request->username(), $request->email(), $request->password());

        try {
            $output = $registerUser->register($input);
        } catch (CannotRegisterUserException $exception) {
            return redirect()->back()->withErrors([
                $exception->getMessage(),
            ]);
        }

        Auth::loginUsingId($output->userId);
        Session::regenerate();
        Session::regenerateToken();

        return redirect()->route('dashboard');
    }
}
