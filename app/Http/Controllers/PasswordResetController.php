<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function sendLink(ForgotPasswordRequest $request)
    {
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

    public function reset(ResetPasswordRequest $request)
    {
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
