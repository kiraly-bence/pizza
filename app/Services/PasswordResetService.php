<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    public function sendResetLink(string $email): void
    {
        Password::sendResetLink(compact('email'));
    }

    public function reset(array $data): string
    {
        return Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        });
    }
}
