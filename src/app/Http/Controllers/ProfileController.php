<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Acme\Application\UseCase\ShowUserProfile\ShowUserProfileUseCase;
use Acme\Application\UseCase\ShowUserProfile\ShowUserProfileUseCaseInput;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

final class ProfileController extends Controller
{
    public function __invoke(ShowUserProfileUseCase $useCase): View
    {
        /** @var User $user */
        $user = Auth::user();
        $input = new ShowUserProfileUseCaseInput($user->username);
        $output = $useCase->show($input);

        return view('profile', [
            'username' => $output->username,
            'email' => $output->email,
        ]);
    }
}
