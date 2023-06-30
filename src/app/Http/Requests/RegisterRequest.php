<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'email' => ['required', 'email'],
        ];
    }

    public function username(): string
    {
        return $this->input('username');
    }

    public function password(): string
    {
        return $this->input('password');
    }

    public function email(): string
    {
        return $this->input('email');
    }
}
