<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Az e-mail cím megadása kötelező.',
            'email.email'    => 'Érvényes e-mail címet adj meg.',
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('forgot_status', 'sent');
    }

    public function showReset(Request $request, string $token)
    {
        return Inertia::render('ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'email.required'     => 'Az e-mail cím megadása kötelező.',
            'email.email'        => 'Érvényes e-mail címet adj meg.',
            'password.required'  => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A két jelszó nem egyezik meg.',
            'password.min'       => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('home')->with('reset_success', true);
        }

        return back()->withErrors([
            'email' => match ($status) {
                Password::INVALID_TOKEN => 'Érvénytelen vagy lejárt hivatkozás. Kérj új jelszó-visszaállítási e-mailt.',
                Password::INVALID_USER  => 'Nem található fiók ezzel az e-mail címmel.',
                default                 => 'Valami hiba történt. Próbáld újra.',
            },
        ]);
    }
}
